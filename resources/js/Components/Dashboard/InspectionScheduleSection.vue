<template>
  <div class="row mb-5">
    <div class="col-12">
      <h2 class="mb-3">Inspection Schedule</h2>
      <div class="card">
        <div class="card-body p-2 p-md-3">
          <div ref="calendarEl"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, watch } from 'vue';
  import { Calendar } from '@fullcalendar/core';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import interactionPlugin from '@fullcalendar/interaction';

  const props = defineProps({
    schedules: {
      type: Array,
      required: true
    }
  });

  const calendarEl = ref(null);
  let calendar = null;

  const getStatusClass = (status) => {
    const statusMap = {
      'upcoming': 'primary',
      'now': 'info',
      'done': 'warning',
      'cancelled': 'danger'
    };
    return statusMap[status] || 'secondary';
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

  :deep(.inspection-event-upcoming) {
    background-color: transparent !important;
  }

  :deep(.inspection-event-now) {
    background-color: transparent !important;
  }

  :deep(.inspection-event-done) {
    background-color: transparent !important;
  }

  :deep(.inspection-event-cancelled) {
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

  /* Mobile responsive */
  @media (max-width: 768px) {
    :deep(.fc-toolbar) {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    :deep(.fc-toolbar-chunk) {
      display: flex;
      justify-content: center;
    }
    
    :deep(.fc-toolbar-title) {
      font-size: 1rem !important;
    }
    
    :deep(.fc-button) {
      padding: 0.2rem 0.4rem;
      font-size: 0.75rem;
    }
    
    :deep(.fc-daygrid-day-number) {
      font-size: 0.7rem;
      padding: 2px;
    }

    :deep(.fc-daygrid-day-frame) {
      min-height: 60px;
    }
    
    :deep(.inspection-badge) {
      font-size: 0.55rem !important;
      padding: 1px 3px;
    }

    :deep(.fc-event) {
      padding: 1px 2px;
      margin-bottom: 1px;
    }
  }

  @media (max-width: 576px) {
    :deep(.fc) {
      font-size: 0.75rem;
    }
    
    :deep(.fc-col-header-cell-cushion) {
      padding: 2px;
      font-size: 0.65rem;
    }

    :deep(.fc-daygrid-day-number) {
      font-size: 0.65rem;
    }

    :deep(.inspection-badge) {
      font-size: 0.5rem !important;
      padding: 1px 2px;
    }

    :deep(.fc-daygrid-day-frame) {
      min-height: 50px;
    }
  }
</style>