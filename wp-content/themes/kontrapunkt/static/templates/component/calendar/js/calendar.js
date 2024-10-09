import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import { useGetDataset } from './useGetDataset';

const calendarEl = document.getElementById('calendar');
const calendarData = useGetDataset(calendarEl, {
  events: [],
});

const calendar = new Calendar(calendarEl, {
  plugins: [ dayGridPlugin ],
  navLinks: false,
  events: calendarData.events,
  eventDisplay: 'block',
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    meridiem: false
  },
  displayEventEnd: true,
  initialView: 'dayGridMonth',
  locale: 'cs',
  headerToolbar: {
    left: 'title',
    center: '',
    right: 'prev,next',
  },
  eventColor: '#ffcc02',
  eventTextColor: '#000',

});
calendar.render();
