<template>
  <div class="antialiased bg-secondary text-dark d-flex flex-column min-vh-100">
    <!--Large Screen Flash Message-->
    <div class="row d-flex flex-row-reverse d-none d-md-flex">
      <div class="col-3">
        <FlashMessage class="alert-container me-3" data-controller="flash-message"/>
      </div>
    </div>
    <!--end large screen flash message-->

    <!--Small Screen Flash Message-->
    <div class="d-md-none d-sm-block ms-3">
      <FlashMessage class="alert-container me-3" data-controller="flash-message"/>
    </div>
    <!--end small screen flash message-->
    <main class="flex-grow-1 " data-controller="want-to-help-btn">
      <Navigation />
      <slot></slot>
    </main>
    <Footer></Footer>
  </div>
</template>

<script setup>
  import FlashMessage from '../Components/FlashMessage.vue';
  import Footer from '../Components/Footer.vue';
  import Navigation from '../Components/Navigation.vue';
</script>