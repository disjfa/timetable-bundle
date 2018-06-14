import axios from 'axios';

export default {
  getTimetable(state, url) {
    axios
      .get(url)
      .then(res => {
        state.commit('setupTimetable', res.data.data);
      });
  },
  saveTimetable(state, payload) {
    axios
      .patch(payload.url, {
        'timetable': payload.timetable
      })
      .then(res => {
        state.commit('setupTimetable', res.data.data);
      });
  }
};
