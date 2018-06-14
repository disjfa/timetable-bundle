import _ from 'underscore';

export default {
  getTimetable: state => _.clone(state.timetable),
  getPlaces: state => _.clone(state.places),
  getDates: state => _.clone(state.dates),
  getItems: state => _.clone(state.items),
};
