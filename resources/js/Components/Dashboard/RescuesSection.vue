<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Rescues</h2>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <h5 class="card-title">Total Rescues</h5>
          <h2 class="card-text">{{ rescueStats.total }}</h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <h5 class="card-title">Adopted</h5>
          <h2 class="card-text">{{ rescueStats.adopted }}</h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <h5 class="card-title">Available for Adoption</h5>
          <h2 class="card-text">{{ rescueStats.available }}</h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <h5 class="card-title">In Shelter</h5>
          <h2 class="card-text">{{ rescueStats.inShelter }}</h2>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-header">
          <h5>Rescues by Month</h5>
        </div>
        <div class="card-body">
          <canvas ref="rescuesChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, onBeforeUnmount, computed } from 'vue'

  const props = defineProps({
    rescues: {
      type: Array,
      required: true
    }
  })

  const Chart = window.Chart || (() => {
    console.warn('Chart.js not loaded')
    return { register: () => {}, Chart: class {} }
  })

  const rescuesChart = ref(null)
  let chartInstance = null 

  const rescueStats = computed(() => ({
    total: props.rescues.length,
    adopted: props.rescues.filter(r => r.adoption_status === 'adopted').length,
    available: props.rescues.filter(r => r.adoption_status === 'available').length,
    inShelter: props.rescues.filter(r => r.adoption_status !== 'adopted').length
  }))

  const rescuesByMonth = computed(() => {
    const counts = Array(12).fill(0)
    props.rescues.forEach(r => {
      const month = new Date(r.created_at).getMonth()
      counts[month]++
    })
    return counts
  })

  const availableByMonth = computed(() => {
    const counts = Array(12).fill(0)
    props.rescues
      .filter(r => r.adoption_status === 'available')
      .forEach(r => {
        const month = new Date(r.created_at).getMonth()
        counts[month]++
      })
    return counts
  })

  const createRescuesChart = () => {
    if (!rescuesChart.value) return

    // destroy old chart if exists (important for resize handling)
    if (chartInstance) {
      chartInstance.destroy()
    }

    const ctx = rescuesChart.value.getContext('2d')
    chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: 'Total Rescues',
            data: rescuesByMonth.value,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
          },
          {
            label: 'Available for Adoption',
            data: availableByMonth.value,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    })
  }

  const handleResize = () => {
    createRescuesChart()
  }

  onMounted(() => {
    createRescuesChart()
    window.addEventListener('resize', handleResize)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
    if (chartInstance) {
      chartInstance.destroy()
    }
  })
</script>

<style scoped>
  .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
  }

  .card-body h2 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
  }

  .card-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }

  canvas {
    width: 100% !important;
    height: 400px !important; 
  }
</style>