<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Reports</h2>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">Total Reports</h5>
            <a :href="`/dashboard/reports`" class="fs-6 text-dark font-monospace fw-bold">Manage Reports</a>
          </div>
          <h2 class="card-text">
            <CountUp :value="reportStats.total" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title">Lost Reports</h5>
          <h2 class="card-text">
            <CountUp :value="reportStats.lost" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-dark">
        <div class="card-body">
          <h5 class="card-title">Found Reports</h5>
          <h2 class="card-text">
            <CountUp :value="reportStats.found" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-dark">
        <div class="card-body">
          <h5 class="card-title">Active Reports</h5>
          <h2 class="card-text">
            <CountUp :value="reportStats.active" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Reports by Month</h5>
          <div class="d-flex align-items-center">
            <label for="yearSelect" class="form-label me-2 mb-0 text-muted">Year:</label>
            <select 
              id="yearSelectReport"
              v-model="selectedYear" 
              @change="updateReportChart"
              class="form-select form-select-sm"
              style="width: auto; min-width: 100px;"
            >
              <option v-for="year in availableYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <canvas ref="reportsChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
  import CountUp from './CountUp.vue'
  
  const props = defineProps({
    reports:{
      type: Array,
      required: true
    }
  })

  const Chart = window.Chart

  const reportsChart = ref(null)
  const selectedYear = ref(new Date().getFullYear())
  let chartInstance = null
  let observer = null

  const reportStats = computed(() => ({
    total: props.reports.length,
    lost: props.reports.filter(r => r.type === 'lost').length,
    found: props.reports.filter(r => r.type === 'found').length,
    active: props.reports.filter(r => r.status === 'active').length,
  }))

  const availableYears = computed(() => {
    const currentYear = new Date().getFullYear()
    const years = []
    for (let year = currentYear; year >= 2018; year--) {
      years.push(year)
    }
    return years
  })

  const filteredReports = computed(() => {
    return props.reports.filter(r => {
      const reportYear = new Date(r.created_at).getFullYear()
      return reportYear === selectedYear.value
    })
  })

  const lostReportByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredReports.value
      .filter(r => r.type === 'lost')
      .forEach(r => {
        const month = new Date(r.created_at).getMonth()
        counts[month]++
      })
    return counts
  })

  const foundReportByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredReports.value
      .filter(r => r.type === 'found')
      .forEach(r => {
        const month = new Date(r.created_at).getMonth()
        counts[month]++
      })
    return counts
  })

  const createReportsChart = () => {
    if (!reportsChart.value) return
    
    if (chartInstance) {
      chartInstance.destroy()
    }
    const ctx = reportsChart.value.getContext('2d')
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: 'Lost Reports',
            data: lostReportByMonth.value,
            backgroundColor: 'rgba(255, 199, 0, 1)'
          },
          {
            label: 'Found Reports',
            data: foundReportByMonth.value,
            backgroundColor: 'rgba(25, 135, 84, 0.8)'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1200,
          easing: "easeOutQuart"
        },
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: `Lost-and-Found Reports in ${selectedYear.value}`
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }

  const updateReportChart = () => {
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    createReportsChart()
  }

  const handleResize = () => {
    createReportsChart()
  }

  watch([lostReportByMonth,foundReportByMonth,selectedYear], () => {
    updateReportChart()
  })

  onMounted(() => {
    if (availableYears.value.length > 0) {
      selectedYear.value = availableYears.value[0] 
    }
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          createReportsChart()
        } else {
          
          if (chartInstance) {
            chartInstance.destroy()
            chartInstance = null
          }
        }
      })
    }, { threshold: 0.3 }) 

    if (reportsChart.value) {
      observer.observe(reportsChart.value)
    }

    window.addEventListener('resize', handleResize)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
    if (observer && reportsChart.value) {
      observer.unobserve(reportsChart.value)
    }
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

  .form-select-sm {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
  }
</style>