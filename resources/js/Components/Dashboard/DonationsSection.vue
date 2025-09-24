<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Donations</h2>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">Total Donations</h5>
            <a :href="`/dashboard/donations`" class="fs-6 text-dark font-monospace fw-bold">Manage Donations</a>
          </div>
          <h2 class="card-text">
            <CountUp :value="donationStats.total" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-danger text-dark">
        <div class="card-body">
          <h5 class="card-title">Monetary Donations</h5>
          <h2 class="card-text">
            <CountUp :value="donationStats.monetary" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title">In-kind Donations</h5>
          <h2 class="card-text">
            <CountUp :value="donationStats.inKind" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-dark">
        <div class="card-body">
          <h5 class="card-title">Pending Donations</h5>
          <h2 class="card-text">
            <CountUp :value="donationStats.pending" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Donations by Month</h5>
          <div class="d-flex align-items-center">
            <label for="yearSelect" class="form-label me-2 mb-0 text-muted">Year:</label>
            <select 
              id="yearSelectDonation"
              v-model="selectedYear" 
              @change="updateDonationChart"
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
          <canvas ref="donationsChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
  import CountUp from './CountUp.vue'
  
  const props = defineProps({
    donations:{
      type: Array,
      required: true
    }
  })

  const Chart = window.Chart

  const donationsChart = ref(null)
  const selectedYear = ref(new Date().getFullYear())
  let chartInstance = null
  let observer = null

  const donationStats = computed(() => ({
    total: props.donations.length,
    inKind: props.donations.filter(r => r.donation_type === 'in-kind').length,
    monetary: props.donations.filter(r => r.donation_type === 'monetary').length,
    pending: props.donations.filter(r => r.status === 'pending').length,
  }))

  const availableYears = computed(() => {
    const currentYear = new Date().getFullYear()
    const years = []
    for (let year = currentYear; year >= 2018; year--) {
      years.push(year)
    }
    return years
  })

  const filteredDonations = computed(() => {
    return props.donations.filter(r => {
      const donationYear = new Date(r.donation_date).getFullYear()
      return donationYear === selectedYear.value
    })
  })

  const monetaryDonationByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredDonations.value
      .filter(r => r.donation_type === 'monetary')
      .forEach(r => {
        const month = new Date(r.donation_date).getMonth()
        counts[month]++
      })
    return counts
  })

  const inKindDonationByMonth = computed(() => {
    const counts = Array(12).fill(0)
    filteredDonations.value
      .filter(r => r.donation_type === 'in-kind')
      .forEach(r => {
        const month = new Date(r.donation_date).getMonth()
        counts[month]++
      })
    return counts
  })

  const createDonationsChart = () => {
    if (!donationsChart.value) return
    
    if (chartInstance) {
      chartInstance.destroy()
    }
    const ctx = donationsChart.value.getContext('2d')
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: 'Monetary Donations',
            data: monetaryDonationByMonth.value,
            backgroundColor: 'rgba(220, 53, 69, 0.8)'
          },
          {
            label: 'In-Kind Donations',
            data: inKindDonationByMonth.value,
            backgroundColor: 'rgba(255, 193, 7, 0.8)'
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
            text: `Donations in ${selectedYear.value}`
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

  const updateDonationChart = () => {
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    createDonationsChart()
  }

  const handleResize = () => {
    createDonationsChart()
  }

  watch([monetaryDonationByMonth,inKindDonationByMonth,selectedYear], () => {
    updateDonationChart()
  })

  onMounted(() => {
    if (availableYears.value.length > 0) {
      selectedYear.value = availableYears.value[0] 
    }
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          createDonationsChart()
        } else {
          // Optional: destroy when leaving so it re-animates next time
          if (chartInstance) {
            chartInstance.destroy()
            chartInstance = null
          }
        }
      })
    }, { threshold: 0.3 }) // fire when 30% visible

    if (donationsChart.value) {
      observer.observe(donationsChart.value)
    }

    window.addEventListener('resize', handleResize)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
    if (observer && donationsChart.value) {
      observer.unobserve(donationsChart.value)
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