<template>
  <Head title="User/Show"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary">
          <a :href="previousUrl" class="text-decoration-none font-monospace fw-bolder text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back {{ urlText }}</span></a>
        </div>
        <PersonalInformationSection 
          :user="user"
        />

        <DeleteAddressModal/>

        <AddressInformationSection 
          :user="user"
        />

        <DeleteHouseholdModal/>

        <HouseholdInformationSection 
          :user="user"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import PersonalInformationSection from '../../Components/User/PersonalInformationSection.vue';
  import DeleteAddressModal from '../../Components/Modals/Users/DeleteAddressModal.vue';
  import AddressInformationSection from '../../Components/User/AddressInformationSection.vue';
  import DeleteHouseholdModal from '../../Components/Modals/Users/DeleteHouseholdModal.vue';
  import HouseholdInformationSection from '../../Components/User/HouseholdInformationSection.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
    previousUrl: String,
    urlText: String,
  })
</script>