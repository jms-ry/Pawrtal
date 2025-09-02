<template>
  <div class="card mt-5 mt-md-3 mb-4 mb-md-2 border-0">
    <div class="card-body border-0 p-3 p-md-5">
      <div class="container-fluid mx-auto shadow p-3 mb-5 rounded">
        <h3 class="text-center fw-bolder display-6 font-monospace mb-2 mt-3">
          Meet Few of Our Rescues
        </h3>
        <div class="row g-3 mt-4 mx-auto">
          <div
            v-for="(rescue, index) in rescues"
            :key="rescue.id"
            class="col-12 col-md-6 col-lg-3"
            data-aos="zoom-in-up"
            :data-aos-delay="400 + index * 100"
          >
            <div
              class="rescue-card border-0 rounded-4 overflow-hidden shadow-lg position-relative"
              style="height: 250px;"
            >
              <img
                :src="rescue.profile_image_url"
                :alt="rescue.name"
                class="w-100 h-100 object-fit-cover"
              />
              <span
                class="position-absolute top-0 start-0 m-2 px-2 py-1 bg-light bg-opacity-75 text-dark fw-bold rounded"
              >
                {{ rescue.name }}
              </span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center mt-5 mx-auto">
          <a
            :href="route('rescues.index')"
            class="btn btn-warning fw-bold rounded-pill px-4 mt-auto mx-auto"
          >
            See All Rescues
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

// Props from Inertia (passed from Laravel controller)
const props = defineProps({
  rescues: {
    type: Array,
    required: true,
  },
});

</script>
