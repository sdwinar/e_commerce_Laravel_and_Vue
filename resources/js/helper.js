import moment from "moment";

// ***********************************************************
// const moment = require('moment');

// Mapping of numeric months to character months
 const monthMap = {
    "01": "Jan",
    "02": "Feb",
    "03": "Mar",
    "04": "Apr",
    "05": "May",
    "06": "Jun",
    "07": "Jly",
    "08": "Ags",
    "09": "Seb",
    "10": "Oct",
    "11": "Nov",
    "12": "Des",
  };

  // Replace numeric month with character month
  export function convertDateToCharacterMonth(date) {
    const formattedDate = moment(date).format("YYYY-MM-DD");
    const numericMonth = formattedDate.slice(5, 7);
    const characterMonth = monthMap[numericMonth];

    return formattedDate.replace(numericMonth, characterMonth);
  }
