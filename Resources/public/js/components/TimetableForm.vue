<template>
  <form @submit.prevent="submit()" style="background: rgba(0,0,0,.3)" class="py-3 px-3" v-if="myTimetable && myTimetable.links.patch_url">
    <h3>{{ myTimetable.title }}</h3>
    <div class="form-group">
      <label for="timetable_title">Title</label>
      <input class="form-control" v-model="myTimetable.title" id="timetable_title">
    </div>
    <div class="form-group">
      <div class="btn-group">
        <button @click="setSide('vertical')" :class="hasSide('vertical')" class="btn">
          vertical
        </button>
        <button @click="setSide('horizontal')" :class="hasSide('horizontal')" class="btn">
          horizontal
        </button>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">save</button>
  </form>
</template>

<script>
  import Vue from 'vue';

  export default {
    name: "TimetableForm",
    props: {
      timetable: Object,
    },
    data() {
      return {
        myTimetable: null,
      }
    },
    mounted() {
      Vue.set(this, 'myTimetable', this.timetable)
    },
    methods: {
      hasSide(what) {
        if (this.myTimetable.side === what) {
          return 'btn-primary';
        }
        return 'btn-outline-primary';
      },
      setSide(what) {
        this.myTimetable.side = what;
        Vue.set(this.myTimetable, 'side', what);
      },
      submit() {
        this.$store.dispatch('timetable/saveTimetable', {
          url: this.myTimetable.links.patch_url,
          timetable: {
            title: this.myTimetable.title,
            side: this.myTimetable.side,
            _token: this.myTimetable.token,
          },
        });
      }
    }
  }
</script>