<template>
  <span ref="numberRef">{{ displayValue }}</span>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue"

const props = defineProps({
  value: { type: Number, required: true },     
  duration: { type: Number, default: 1000 }   
})

const numberRef = ref(null)
const displayValue = ref(0)

let observer

const animateCount = () => {
  const start = 0
  const end = props.value
  const startTime = performance.now()

  const step = (now) => {
    const progress = Math.min((now - startTime) / props.duration, 1)
    displayValue.value = Math.floor(progress * (end - start) + start)

    if (progress < 1) {
      requestAnimationFrame(step)
    }
  }

  displayValue.value = 0 
  requestAnimationFrame(step)
}

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCount()
      }
    })
  })
  if (numberRef.value) {
    observer.observe(numberRef.value)
  }
})

onBeforeUnmount(() => {
  if (observer && numberRef.value) {
    observer.unobserve(numberRef.value)
  }
})

watch(() => props.value, (newVal) => {
  if (numberRef.value && observer) {
    const rect = numberRef.value.getBoundingClientRect()
    const inView = rect.top >= 0 && rect.bottom <= window.innerHeight
    if (inView) animateCount()
  }
})
</script>
