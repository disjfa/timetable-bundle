<template>
  <div v-if="timetable">
    <h1>{{ timetable.title }}</h1>

    <div v-for="date in timetable.dates">
      <h3>{{ date.title }}</h3>
      <div class="table-responsive">
        <div class="timetable py-3">
          <div v-for="header in date.headers" class="box" :style="headerStyle(header)">
            {{ header.date.date }}
          </div>
          <template v-for="(place, index) in timetable.places">
            <div class="box box-title" :style="placeStyle(index)">
              {{place.title}}
            </div>
            <div v-for="item in timetable.items" class="box" :style="itemStyle(item, index)" v-if="itemInPlaceAndDate(item, place, date)">
              <div>
                <strong>{{ item.title }}</strong>
              </div>
              {{ item.dateStart.date }} - {{ item.dateEnd.date }}
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'timetable',
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
          'grid-row': header.start + '/' + header.end,
          'grid-column': 1,
        }
      },
      placeStyle(index) {
        return {
          'grid-row': 1,
          'grid-column': index + 2,
        }
      },
      itemStyle(item, index) {
        return {
          'grid-row': item.start + '/' + item.end,
          'grid-column': index + 2,
        }
      },
      itemInPlaceAndDate(item, place, date) {
        return item.date === date.id && item.place === place.id;
      }
    }
  }
</script>