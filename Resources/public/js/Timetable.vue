<template>
  <div v-if="timetable.id">
    <h1>{{ timetable.title }}</h1>

    <timetable-form :timetable="timetable"></timetable-form>

    <div v-for="date in dates">
      <h3>{{ date.title }}</h3>
      <div class="table-responsive">
        <div class="timetable py-3">
          <div v-for="header in date.headers" class="box" :style="headerStyle(header)">
            <timetable-time :date="header.date"></timetable-time>
          </div>
          <template v-for="(place, index) in places">
            <div class="box box-title" :style="placeStyle(index)">
              {{place.title}}
            </div>
            <div v-for="item in items" class="box" :style="itemStyle(item, index)" v-if="itemInPlaceAndDate(item, place, date)">
              <div>
                <strong>{{ item.title }}</strong>
              </div>
              <timetable-time :date="item.dateStart"></timetable-time>
              -
              <timetable-time :date="item.dateEnd"></timetable-time>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import TimetableTime from './components/TimetableTime';
  import TimetableForm from './components/TimetableForm';

  export default {
    name: 'timetable',
    components: {
      TimetableTime,
      TimetableForm
    },
    props: {
      timetableUrl: String
    },
    mounted() {
      this.$store.dispatch('timetable/getTimetable', this.timetableUrl);
    },
    computed: {
      ...mapGetters({
        // map `this.doneCount` to `this.$store.getters.doneTodosCount`
        timetable: 'timetable/getTimetable',
        places: 'timetable/getPlaces',
        dates: 'timetable/getDates',
        items: 'timetable/getItems',
      })
    },
    methods: {
      headerStyle(header) {
        if (this.timetable.side === 'vertical') {
          return {
            'grid-row': header.start + '/' + header.end,
            'grid-column': 1,
          }
        }
        return {
          'grid-row': 1,
          'grid-column': header.start + '/' + header.end,
        }
      },
      placeStyle(index) {
        if (this.timetable.side === 'vertical') {
          return {
            'grid-row': 1,
            'grid-column': index + 2,
          }
        }
        return {
          'grid-row': index + 2,
          'grid-column': 1,
        }
      },
      itemStyle(item, index) {
        if (this.timetable.side === 'vertical') {
          return {
            'grid-row': item.start + '/' + item.end,
            'grid-column': index + 2,
          }
        }
        return {
          'grid-row': index + 2,
          'grid-column': item.start + '/' + item.end,
        }
      },
      itemInPlaceAndDate(item, place, date) {
        return item.date === date.id && item.place === place.id;
      }
    }
  }
</script>