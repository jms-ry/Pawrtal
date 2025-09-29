<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Adoption Applications</h2>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">Total Application</h5>
             <a :href="`/dashboard/adoption-applications`" class="fs-6 text-dark font-monospace fw-bold">Manage Applications</a>
          </div>
          <h2 class="card-text">
            <CountUp :value="applicationStats.total" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-dark">
        <div class="card-body">
          <h5 class="card-title">Approved Applications</h5>
          <h2 class="card-text">
            <CountUp :value="applicationStats.approved" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title">Pending Applications</h5>
          <h2 class="card-text">
            <CountUp :value="applicationStats.pending" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-dark">
        <div class="card-body">
          <h5 class="card-title">Under Review Applications</h5>
          <h2 class="card-text">
            <CountUp :value="applicationStats.underReview" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Adoption Applications by Month</h5>
          <div class="d-flex align-items-center">
            <label for="yearSelectApplications" class="form-label me-2 mb-0 text-muted">Year:</label>
            <select 
              id="yearSelectApplications"
              v-model="selectedYear" 
              @change="updateChart"
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
          <canvas ref="applicationsChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
  import CountUp from './CountUp.vue'

  const props = defineProps({
    applications: {
      type: Array,
      required: true
    }
  })

  const Chart = window.Chart

  const applicationsChart = ref(null)
  const selectedYear = ref(new Date().getFullYear())
  let chartInstance = null 
  let observer = null
  
  const applicationStats = computed(() => ({
    total: props.applications.length,
    approved: props.applications.filter(a => a.status === 'approved').length,
    pending: props.applications.filter(a => a.status === 'pending').length,
    underReview: props.applications.filter(a => a.status === 'under_review').length
  }))

  const availableYears = computed(() => {
    const currentYear = new Date().getFullYear()
    const years = []
    for (let year = currentYear; year >= 2018; year--) {
      years.push(year)
    }
    return years
  })
  const filteredApplications = computed(() => {
    return props.applications.filter(a => {
      const applicationYear = new Date(a.application_date).getFullYear()
      return applicationYear === selectedYear.value
    })
  })

  const approvedApplicationsByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredApplications.value
      .filter(a => a.status === 'approved')
      .forEach(a => {
        const month = new Date(a.application_date).getMonth()
        counts[month]++
      })
    return counts
  })

  const pendingApplicationsByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredApplications.value
      .filter(a => a.status === 'pending')
      .forEach(a => {
        const month = new Date(a.application_date).getMonth()
        counts[month]++
      })
    return counts
  })

  const underReviewApplicationsByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredApplications.value
      .filter(a => a.status === 'under_review')
      .forEach(a => {
        const month = new Date(a.application_date).getMonth()
        counts[month]++
      })
    return counts
  })


  const createApplicationsChart = () => {
    if (!applicationsChart.value) return

    if (chartInstance) {
      chartInstance.destroy()
    }

    chartInstance = new Chart(applicationsChart.value, {
      type: 'line',
      data: {
        labels: [
          'January', 'February', 'March', 'April', 'May', 'June',
          'July', 'August', 'September', 'October', 'November', 'December'
        ],
        datasets: [
          {
            label: 'Approved Applications',
            data: approvedApplicationsByMonth.value,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
          },
          {
            label: 'Pending Applications',
            data: pendingApplicationsByMonth.value,
            borderColor: 'rgb(255, 159, 64)',
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            tension: 0.1
          },
          {
            label: 'Under Review Applications',
            data: underReviewApplicationsByMonth.value,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
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
            position: 'top'
          },
          title: {
            display: true,
            text: `Adoption Applications in ${selectedYear.value}`
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            precision: 0
          }
        }
      }
    })
  }

  const updateChart = () => {
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    createApplicationsChart()
  }

  const handleResize = () => {
    createApplicationsChart()
  }

  watch([approvedApplicationsByMonth, pendingApplicationsByMonth,selectedYear], () => {
    updateChart()
  })

  onMounted(() => {
    if (availableYears.value.length > 0) {
      selectedYear.value = availableYears.value[0] 
    }
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          createApplicationsChart()
        } else {
          // Optional: destroy when leaving so it re-animates next time
          if (chartInstance) {
            chartInstance.destroy()
            chartInstance = null
          }
        }
      })
    }, { threshold: 0.3 })

    if (applicationsChart.value) {
      observer.observe(applicationsChart.value)
    }

    window.addEventListener('resize', handleResize)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
    if (observer && applicationsChart.value) {
      observer.unobserve(applicationsChart.value)
    }
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
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