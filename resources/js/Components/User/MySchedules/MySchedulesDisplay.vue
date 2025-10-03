<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div class="card">
      <div class="card-body p-2 p-md-3">
        <!-- Calendar View (Desktop Only) -->
        <div class="d-none d-md-block" ref="calendarEl"></div>

       <!-- List View (Mobile Only) -->
        <div class="d-md-none inspection-list">
          <div 
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
          <div v-show="schedule.status !== 'done' && schedule.status !== 'cancelled'" class="mt-3">
            <button class="btn btn-primary btn-sm me-1" :data-schedule-id="schedule.id" data-bs-toggle="modal" data-bs-target="#markDoneModal">Mark Done</button>
            <button class="btn btn-danger btn-sm ms-1" :data-schedule-id="schedule.id" data-bs-toggle="modal" data-bs-target="#cancelInspectionModal">Cancel Inspection</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="calendarActionsModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h6 class="modal-title fw-bold fs-4">Update Inspection</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedSchedule">
            <div class="d-grid gap-2">
              <button class="btn btn-primary" @click="triggerMarkDone">
                <i class="bi bi-check-circle"></i> Mark as Done
              </button>
              <button class="btn btn-danger" @click="triggerCancel">
                <i class="bi bi-x-circle"></i> Cancel Inspection
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <MarkDoneModal/>
    <CancelInspectionModal/>
  </div>
</div>
</template>

<script setup>
  import { ref, onMounted, watch, computed } from 'vue';
  import { Calendar } from '@fullcalendar/core';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import MarkDoneModal from '../../Modals/Users/MySchedules/MarkDoneModal.vue';
  import CancelInspectionModal from '../../Modals/Users/MySchedules/CancelInspectionModal.vue';
  import * as bootstrap from 'bootstrap';

  const props = defineProps({
    schedules: {
      type: Array,
      required: true
    },
  })

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

  const selectedSchedule = ref(null);
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
      eventClick: (info) => {
        info.jsEvent.preventDefault();
        const schedule = props.schedules.find(s => s.id === parseInt(info.event.id));
        
        if (schedule && (schedule.status !== 'done' && schedule.status !== 'cancelled')) {
          selectedSchedule.value = schedule;
          const modalEl = document.getElementById('calendarActionsModal');
          if (modalEl) {
            const actionsModal = new bootstrap.Modal(modalEl); 
            actionsModal.show();
          }
        }
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

  const triggerMarkDone = () => {
    if (selectedSchedule.value) {
      const modalEl = document.getElementById('calendarActionsModal');
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) {
        modalInstance.hide();
      }
      setTimeout(() => {
        const tempBtn = document.createElement('button');
        tempBtn.setAttribute('data-schedule-id', selectedSchedule.value.id);
        tempBtn.setAttribute('data-bs-toggle', 'modal');
        tempBtn.setAttribute('data-bs-target', '#markDoneModal');
        document.body.appendChild(tempBtn);
        tempBtn.click();
        tempBtn.remove();
      }, 300);
    }
  };

  const triggerCancel = () => {
    if (selectedSchedule.value) {
      const modalEl = document.getElementById('calendarActionsModal');
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) {
        modalInstance.hide();
      }
      
      setTimeout(() => {
        const tempBtn = document.createElement('button');
        tempBtn.setAttribute('data-schedule-id', selectedSchedule.value.id);
        tempBtn.setAttribute('data-bs-toggle', 'modal');
        tempBtn.setAttribute('data-bs-target', '#cancelInspectionModal');
        document.body.appendChild(tempBtn);
        tempBtn.click();
        tempBtn.remove();
      }, 300);
    }
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
</style>