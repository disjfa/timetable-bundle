<template>
  <form @submit.prevent="submit()" style="background: rgba(0,0,0,.3)" class="py-3 px-3">
    <h3>{{ timetable.title }}</h3>
    <div class="form-group">
      <label for="timetable_title">Title</label>
      <input class="form-control" v-model="timetable.title" id="timetable_title">
    </div>
    <div class="form-group">
      <div class="custom-control custom-radio">
        <input type="radio" id="timetable_side_vertical" class="custom-control-input" v-model="timetable.side" value="vertical">
        <label class="custom-control-label" for="timetable_side_vertical">Vertical</label>
      </div>
      <div class="custom-control custom-radio">
        <input type="radio" id="timetable_side_horizontal" class="custom-control-input" v-model="timetable.side" value="horizontal">
        <label class="custom-control-label" for="timetable_side_horizontal">Horizontal</label>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">save</button>
  </form>
</template>

<script>
  import axios from 'axios';

  export default {
    name: "TimetableForm",
    props: {
      timetable: Object,
    },
    mounted() {
      // axios.get()
    },
    methods: {
      submit() {
        axios
          .patch(this.timetable.links.patch_url, {
            'timetable': {
              title: this.timetable.title,
              side: this.timetable.side,
              _token: this.timetable.token,
            },
          });
      }
    }
  }
</script>