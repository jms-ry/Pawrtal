<template>
  <Head title="User/Show"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
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
  })
</script>