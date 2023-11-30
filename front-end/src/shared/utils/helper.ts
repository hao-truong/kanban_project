import moment from 'moment';

const Helper = {
    formatDate: (date: Date): string => {
        return moment(date).format('DD/MM/YYYY');
    }
}

export default Helper;
