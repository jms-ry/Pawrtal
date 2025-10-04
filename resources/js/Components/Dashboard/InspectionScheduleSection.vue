<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Inspection Schedules</h2>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-dark">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">Total Inspection Schedules</h5>
            <a :href="`/users/my-schedules`" class="fs-6 text-dark font-monospace fw-bold ms-1">My Schedules</a>
          </div>
          <h2 class="card-text">
            <CountUp :value="scheduleStats.total" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title">Upcoming Inspection Schedules</h5>
          <h2 class="card-text">
            <CountUp :value="scheduleStats.upcoming" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-dark">
        <div class="card-body">
          <h5 class="card-title">Done Inspection Schedules</h5>
          <h2 class="card-text">
            <CountUp :value="scheduleStats.done" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-dark">
        <div class="card-body">
          <h5 class="card-title">Today Inspection Schedule</h5>
          <h2 class="card-text">
            <CountUp :value="scheduleStats.today" :duration="1200" />
          </h2>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-body p-2 p-md-3">
          <!-- Calendar View (Desktop Only) -->
          <div class="d-none d-md-block" ref="calendarEl"></div>

          <!-- List View (Mobile Only) -->
          <div class="d-md-none inspection-list">
            <div v-if="sortedSchedules.length === 0" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
              <p>No inspection schedules found</p>
            </div>
            <div 
              v-else
              v-for="schedule in sortedSchedules" 
              :key="schedule.id"
              class="inspection-list-item mb-3 p-3 border rounded"
              :class="`border-start-${getStatusClass(schedule.status)} border-start-4`"
            >
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 fw-bold">{{ schedule.inspector_name || 'Unassigned' }}</h6>
                <span :class="`badge bg-${getStatusClass(schedule.status)}`">
                  {{ schedule.status.toUpperCase() }}
                </span>
              </div>
              <div class="text-muted small mb-1">
                <i class="bi bi-geo-alt"></i> {{ schedule.inspection_location }}
              </div>
              <div class="text-muted small">
                <i class="bi bi-calendar-event"></i> {{ formatDate(schedule.inspection_date) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, watch, computed } from 'vue';
  import { Calendar } from '@fullcalendar/core';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import CountUp from './CountUp.vue'

  const props = defineProps({
    schedules: {
      type: Array,
      required: true
    }
  });
  const scheduleStats = computed(() => ({
    total: props.schedules.length,
    upcoming: props.schedules.filter(r => r.status === 'upcoming').length,
    done: props.schedules.filter(r => r.status === 'done').length,
    today: props.schedules.filter(r => r.status === 'now').length,
  }))

  const calendarEl = ref(null);
  let calendar = null;

  const getStatusClass = (status) => {
    const statusMap = {
      'upcoming': 'primary',
      'now': 'info',
      'done': 'success',
      'cancelled': 'warning',
      'missed': 'danger'
    };
    return statusMap[status] || 'secondary';
  };

  const sortedSchedules = computed(() => {
    const statusOrder = {
      'now': 1,
      'upcoming': 2,
      'done': 3,
      'missed': 4,
      'cancelled': 5
    };

    return [...props.schedules].sort((a, b) => {

      const statusDiff = (statusOrder[a.status] || 999) - (statusOrder[b.status] || 999);
      if (statusDiff !== 0) return statusDiff;
      
      return new Date(a.inspection_date) - new Date(b.inspection_date);
    });
  });

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const formatEvents = () => {
    return props.schedules.map(schedule => ({
      id: schedule.id,
      title: schedule.inspector_name || 'Unassigned',
      start: schedule.inspection_date,
      extendedProps: {
        inspectorName: schedule.inspector_name || 'Unassigned',
        location: schedule.inspection_location,
        status: schedule.status
      },
      className: `inspection-event-${schedule.status}`
    }));
  };

  const initializeCalendar = () => {
    if (!calendarEl.value) return;

    calendar = new Calendar(calendarEl.value, {
      plugins: [dayGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek'
      },
      events: formatEvents(),
      eventContent: (arg) => {
        const { inspectorName, location, status } = arg.event.extendedProps;
        const statusClass = getStatusClass(status);
        
        return {
          html: `
            <div class="inspection-event-wrapper">
              <div class="badge bg-${statusClass} fw-bold text-dark mb-1 d-block text-truncate inspection-badge" title="${inspectorName}">
                ${inspectorName}
              </div>
              <div class="badge bg-${statusClass} fw-bold text-dark mb-1 d-block text-truncate inspection-badge" title="${location}">
                ${location}
              </div>
              <div class="badge bg-${statusClass} fw-bold text-dark d-block text-truncate inspection-badge" title="${status.toUpperCase()}">
                ${status.toUpperCase()}
              </div>
            </div>
          `
        };
      },
      height: 'auto',
      contentHeight: 'auto',
      aspectRatio: 1.8,
      displayEventTime: false,
      eventDisplay: 'block',
      dayMaxEvents: true,
      moreLinkText: 'more',
      views: {
        dayGridMonth: {
          dayMaxEvents: 2
        },
        dayGridWeek: {
          dayMaxEvents: false
        }
      }
    });

    calendar.render();
  };

  onMounted(() => {
    initializeCalendar();
  });

  watch(() => props.schedules, () => {
    if (calendar) {
      calendar.removeAllEvents();
      calendar.addEventSource(formatEvents());
    }
  }, { deep: true });
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

  /* List View Styles (Mobile Only) */
  .inspection-list-item {
    background-color: #fff;
    transition: all 0.2s;
  }

  .inspection-list-item:hover {
    background-color: #f8f9fa;
  }

  /* Calendar Styles (Desktop) */
  :deep(.fc) {
    font-size: 0.9rem;
  }

  :deep(.fc-toolbar-title) {
    font-size: 1.2rem !important;
    font-weight: 600;
  }

  :deep(.fc-button) {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }

  :deep(.fc-event) {
    border: none !important;
    padding: 2px 4px;
    margin-bottom: 2px;
    cursor: default;
  }

  :deep(.inspection-event-upcoming),
  :deep(.inspection-event-now),
  :deep(.inspection-event-done),
  :deep(.inspection-event-cancelled),
  :deep(.inspection-event-missed) {
    background-color: transparent !important;
  }

  :deep(.inspection-event-wrapper) {
    padding: 1px;
  }

  :deep(.inspection-badge) {
    font-size: 0.65rem;
    padding: 2px 4px;
    font-weight: 500;
    line-height: 1.2;
  }

  :deep(.fc-daygrid-event-harness) {
    margin-bottom: 2px;
  }

  :deep(.fc-daygrid-day-frame) {
    min-height: 80px;
  }

  @media (max-width: 768px) {
    .card-body h5.card-title {
      font-size: 0.9rem;
    }
    
    .card-body h2.card-text {
      font-size: 1.75rem;
    }
  }
</style>