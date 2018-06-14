<template>
  <span>
    {{ time }}
  </span>
</template>

<script>
  export default {
    name: "TimetableTime",
    props: {
      date: Object,
    },
    computed: {
      time() {
        const date = new Date(this.date.date);
        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
      }
    }
  }
</script>
