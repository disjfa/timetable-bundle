<template>
  <form @submit.prevent="submit()">
    <h3>{{timetableId}}</h3>
  </form>
</template>

<script>
  import axios from 'axios';

  export default {
    name: "TimetableForm",
    props: {
      timetableId: String,
    },
    mounted() {
      // axios.get()
    },
    methods: {
      submit() {
        console.log('a');
      }
    }
  }
</script>