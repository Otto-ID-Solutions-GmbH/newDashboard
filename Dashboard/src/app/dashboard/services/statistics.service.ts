import {Injectable} from '@angular/core';
import * as moment from "moment";

@Injectable({
  providedIn: 'root'
})
export class StatisticsService {

  constructor() {
  }

  reviver(key, value) {

    if (typeof value === "string" && moment(value).isValid()) {
      return new Date(value);
    }
    return value;
  }

  xAxisTickFormatting(value: Date) {
    return value ? value.toLocaleString() : value;
  }
}
