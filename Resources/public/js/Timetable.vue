<template>
  <div v-if="timetable.data">
    <h1>{{ timetable.data.title }}</h1>

    <timetable-form :timetable="timetable.data"></timetable-form>

    <div v-for="date in timetable.data.dates.data">
      <h3>{{ date.title }}</h3>
      <div class="table-responsive">
        <div class="timetable py-3">
          <div v-for="header in date.headers" class="box" :style="headerStyle(header)">
            <timetable-time :date="header.date"></timetable-time>
          </div>
          <template v-for="(place, index) in timetable.data.places.data">
            <div class="box box-title" :style="placeStyle(index)">
              {{place.title}}
            </div>
            <div v-for="item in timetable.items" class="box" :style="itemStyle(item, index)" v-if="itemInPlaceAndDate(item, place, date)">
              <div>
                <strong>{{ item.title }}</strong>
              </div>
              <timetable-time :date="item.dateStart"></timetable-time> - <timetable-time :date="item.dateEnd"></timetable-time>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
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
    data() {
      return {
        timetable: {},
      }
    },
    mounted() {
      axios
        .get(this.timetableUrl)
        .then(res => {
          this.timetable = res.data;
        });
    },
    methods: {
      headerStyle(header) {
        return {
          'grid-row': 1,
          'grid-column': header.start + '/' + header.end,
        }
      },
      placeStyle(index) {
        return {
          'grid-row': index + 2,
          'grid-column': 1,
        }
      },
      itemStyle(item, index) {
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