import Vue from 'vue';

export default {
  setupTimetable(state, payload) {
    //
    const { places, dates, items } = payload;

    delete payload.places;
    delete payload.dates;
    delete payload.items;

    Vue.set(state, 'timetable', payload);
    Vue.set(state, 'places', places.data);
    Vue.set(state, 'dates', dates.data);
    Vue.set(state, 'items', items.data);
  },
};
