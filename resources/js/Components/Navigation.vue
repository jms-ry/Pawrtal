<template>
  <nav class="navbar navbar-expand-lg bg-success pe-2 ps-2 border-0" data-bs-theme="light">
    <div class="container-fluid ms-lg-5">
      <!-- Logo -->
      <div class="d-flex align-items-center">
        <img :src="`/images/pets.png`" alt="Logo" height="40" class="me-1 fw-bolder" />
        <a class="navbar-brand fs-1 fw-bolder" :href="`/`">Pawrtal</a>
      </div>

      <!-- Toggler -->
      <button class="navbar-toggler border border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarColor01">
        <!-- Small Screen -->
        <ul class="navbar-nav me-auto d-lg-none border-top border-1 border-white pt-2 mt-3">
          <li><a class="nav-link fw-bold fs-5" :href="`/rescues`">Rescues</a></li>
          <li><a class="nav-link fw-bold fs-5" :href="`/reports`">Lost-and-Found Reports</a></li>
          <li><a class="nav-link fw-bold fs-5" :href="`/adoption`">Adopt a Rescue</a></li>
          <li><a class="nav-link fw-bold fs-5" :href="`/donate`">Donate</a></li>

          <!-- Guest -->
          <template v-if="!user">
            <li><a class="nav-link fw-bold fs-5" :href="`/register`">Register</a></li>
            <li><a class="nav-link fw-bold fs-5" :href="`/login`">Log in</a></li>
          </template>

          <!-- Auth -->
          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link fw-bold fs-5" data-bs-toggle="dropdown" aria-expanded="false">
                {{ user.name }} <i class="bi bi-caret-down-fill ms-2"></i>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" :href="`/users/${user.id}`">Profile</a></li>
                <template v-if="user.isAdminOrStaff">
                  <li><a class="dropdown-item" href="/dashboard">Manage</a></li>
                  <li><a class="dropdown-item" href="/users/my-schedules">My Schedules</a></li>
                </template>
                <template v-else>
                  <li><a class="dropdown-item" href="/users/my-adoption-applications">My Adoption Applications</a></li>
                </template>
                <li><a class="dropdown-item" href="/users/my-donations">My Donation History</a></li>
                <li><a class="dropdown-item" href="/users/my-reports">My Reports</a></li>
                <li>
                  <a class="dropdown-item" href="/users/my-notifications">
                    <span class="position-relative">
                      Notifications
                      <span
                        v-if="unreadCount > 0"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.7rem; transform: translate(-30%, -40%);"
                      >
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                      </span>
                    </span>
                  </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <form method="POST" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <button class="dropdown-item border-0" type="submit">Logout</button>
                  </form>
                </li>
              </ul>
            </li>
          </template>
        </ul>

        <!-- Large Screen -->
        <div class="d-none d-sm-flex flex-column flex-sm-row">
          <a :href="`/rescues`" class="btn btn-outline-success me-md-1 fw-bold fs-5 border-0">Rescues</a>
          <a :href="`/reports`" class="btn btn-outline-success me-md-1 fw-bold fs-5 border-0">Lost-and-Found Reports</a>
          <a :href="`/adoption`" class="btn btn-outline-success me-md-1 fw-bold fs-5 border-0">Adopt a Rescue</a>
          <a :href="`/donate`" class="btn btn-outline-success me-md-1 fw-bold fs-5 border-0">Donate</a>
        </div>

        <div class="d-none d-sm-flex flex-column flex-sm-row justify-content-end ms-auto">
          <!-- Guest -->
          <template v-if="!user">
            <a :href="`/register`" class="btn btn-warning fw-bolder me-1" style="background-color: #FFF44F;">
              <i class="bi bi-person me-1 fs-5"></i> Register
            </a>
            <a :href="`/login`" class="btn btn-info fw-bolder" style="background-color: #82EEFD;">
              <i class="bi bi-box-arrow-in-right me-1 fs-5"></i> Log in
            </a>
          </template>

          <!-- Auth -->
          <template v-else>
            <div class="btn-group">
              <div class="dropdown-center position-relative">
                <!-- Bell Button -->
                <button
                  class="btn btn-outline-success border-0 fw-bolder position-relative"
                  type="button"
                  data-bs-toggle="dropdown"
                >
                  <span class="position-relative">
                    <i class="bi bi-bell me-1 fs-4 fw-bold"></i>
                    <!-- Badge -->
                    <span
                      v-if="unreadCount > 0"
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="font-size: 0.65rem; transform: translate(-40%, -40%);"
                    >
                      {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                  </span>
                </button>

                <!-- Notification Dropdown (Preview Only) -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 380px; max-height: 450px;">
                  <!-- Header -->
                  <li class="px-3 py-2 border-bottom">
                    <span class="fw-bold">Recent Notifications</span>
                  </li>

                  <!-- Empty State -->
                  <li v-if="unreadNotifications.length === 0" class="text-center text-muted py-4">
                    <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                    <p class="mb-0">No new notifications</p>
                  </li>

                  <!-- Notification List (Preview - First 10 Unread) -->
                  <template v-else>
                    <div style="max-height: 350px; overflow-y: auto;">
                      <li
                        v-for="notif in unreadNotifications.slice(0, 10)"
                        :key="notif.id"
                        class="border-bottom bg-light"
                        style="cursor: pointer;"
                      >
                        <a :href="`/users/my-notifications`" class="text-decoration-none text-dark d-block px-3 py-2">
                          <div class="d-flex align-items-start">
                            <!-- Content -->
                            <div class="flex-grow-1 me-2">
                              <p class="mb-1 small fw-semibold">{{ notif.data.message }}</p>
                              <small class="text-muted">{{ timeAgo(notif.created_at) }}</small>
                            </div>
                            <!-- Unread Dot -->
                            <div class="flex-shrink-0">
                              <span class="d-inline-block bg-primary rounded-circle" style="width: 10px; height: 10px;"></span>
                            </div>
                          </div>
                        </a>
                      </li>
                    </div>

                    <!-- Footer - View All Button -->
                    <li class="text-center py-2 border-top">
                      <a href="/users/my-notifications" class="btn btn-sm btn-primary fw-semibold w-75">
                        View All Notifications
                        <span v-if="unreadCount > 10" class="badge bg-light text-dark ms-1">{{ unreadCount }}</span>
                      </a>
                    </li>
                  </template>
                </ul>
              </div>

              <!-- Gear Dropdown -->
              <div class="dropdown-center">
                <button class="btn btn-outline-success border-0 fw-bolder me-3" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-gear fs-4 fw-bold"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" :href="`/users/${user.id}`">Profile</a></li>
                  <template v-if="user.isAdminOrStaff">
                    <li><a class="dropdown-item" href="/dashboard">Manage</a></li>
                    <li><a class="dropdown-item" href="/users/my-schedules">My Schedules</a></li>
                  </template>
                  <template v-else>
                    <li><a class="dropdown-item" href="/users/my-adoption-applications">My Adoption Applications</a></li>
                  </template>
                  <li><a class="dropdown-item" href="/users/my-donations">My Donation History</a></li>
                  <li><a class="dropdown-item" href="/users/my-reports">My Reports</a></li>
                  <li><hr class="dropdown-divider" /></li>
                  <li>
                    <form method="POST" action="/logout">
                      <input type="hidden" name="_token" :value="csrfToken" />
                      <button class="dropdown-item border-0" type="submit">Logout</button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const unreadNotifications = computed(() => page.props.unreadNotifications ?? [])
const unreadCount = computed(() => page.props.unreadCount ?? 0)
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

// Time ago helper
const timeAgo = (timestamp) => {
  const diff = Math.floor((new Date() - new Date(timestamp)) / 1000)
  if (diff < 60) return 'just now'
  if (diff < 3600) return `${Math.floor(diff / 60)} min ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)} hr ago`
  return `${Math.floor(diff / 86400)} day${Math.floor(diff / 86400) > 1 ? 's' : ''} ago`
}
</script>