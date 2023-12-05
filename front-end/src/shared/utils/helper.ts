import moment from 'moment';
import { RefObject } from 'react';

const Helper = {
  formatDate: (date: Date): string => {
    return moment(date).format('DD/MM/YYYY');
  },
  handleOutSideClick: (ref: RefObject<HTMLElement>, cb: Function): Function => {
    const handleOutsideClick = (event: any) => {
      if (ref.current && !ref.current.contains(event.target)) {
        cb(false);
      }
    };

    document.addEventListener('mousedown', handleOutsideClick);

    return () => {
      document.removeEventListener('mousedown', handleOutsideClick);
    };
  },
};

export default Helper;
