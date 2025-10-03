<template>
  <nav class="navbar navbar-expand-lg bg-success pe-2 ps-2 border-0" data-bs-theme="light">
    <div class="container-fluid ms-lg-5">
      <!-- Logo -->
      <div class="d-flex align-items-center">
        <img :src="`/images/pets.png`" alt="Logo" height="40" class="me-1 fw-bolder" />
        <a class="navbar-brand fs-1 fw-bolder" :href="`/`">Pawrtal</a>
      </div>

      <!-- Toggler -->
      <button class="navbar-toggler border border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

      <div class="collapse navbar-collapse" id="navbarColor01">
        <!-- Small Screen -->
        <ul class="navbar-nav me-auto d-lg-none border-top border-1 border-white pt-2 mt-3">
          <li><a class="nav-link fw-bold fs-5" :href="`/rescues`">Rescues</a></li>
          <li><a  class="nav-link fw-bold fs-5" :href="`/reports`">Lost-and-Found Reports</a></li>
          <li><a  class="nav-link fw-bold fs-5" :href="`/adoption`">Adopt a Rescue</a></li>
          <li><a  class="nav-link fw-bold fs-5" :href="`/donate`">Donate</a></li>

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
                  <li ><a class="dropdown-item" :href="`/dashboard`">Manage</a></li>
                  <li><a class="dropdown-item" :href="`/users/my-schedules`">My Schedules</a></li>
                </template>
                <template v-else>
                  <li><a class="dropdown-item" :href="`/users/my-adoption-applications`">My Adoption Applications</a></li>
                </template>
                <li><a class="dropdown-item" :href="`/users/my-donations`">My Donation History</a></li>
                <li><a class="dropdown-item" :href="`/users/my-reports`">My Reports</a></li>
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
            <div class="dropdown-center me-5">
              <button class="btn btn-outline-success border-0 dropdown-toggle fw-bolder" type="button" data-bs-toggle="dropdown">
                {{ user.name }}
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" :href="`/users/${user.id}`">Profile</a></li>
                <template v-if="user.isAdminOrStaff">
                  <li ><a class="dropdown-item" :href="`/dashboard`">Manage</a></li>
                  <li><a class="dropdown-item" :href="`/users/my-schedules`">My Schedules</a></li>
                </template>
                <template v-else>
                  <li><a class="dropdown-item" :href="`/users/my-adoption-applications`">My Adoption Applications</a></li>
                </template>
                <li><a class="dropdown-item" :href="`/users/my-donations`">My Donation History</a></li>
                <li><a class="dropdown-item" :href="`/users/my-reports`">My Reports</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <form method="POST" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <button class="dropdown-item border-0" type="submit">Logout</button>
                  </form>
                </li>
              </ul>
            </div>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
  import { usePage} from '@inertiajs/vue3'
  import { computed } from 'vue'

  const page = usePage()
  const user = computed(() => page.props.auth.user)
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
</script>
