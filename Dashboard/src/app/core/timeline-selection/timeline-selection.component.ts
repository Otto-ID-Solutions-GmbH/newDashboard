import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {faCalendar} from "@fortawesome/free-solid-svg-icons/faCalendar";

export enum ChartPeriod {
  NOW = 'Now',
  THIS_WEEK = 'This week',
  THIS_MONTH = 'This month',
  LAST_MONTH = 'Last month',
  THIS_YEAR = 'This year',
  LAST_YEAR = 'Last year',
  SINCE_1_WEEK = 'Since 1 week',
  SINCE_1_MONTH = 'Since 1 month',
  SINCE_2_WEEKS = 'Since 2 weeks',
  SINCE_3_MONTHS = 'Since 3 months',
  SINCE_1_YEAR = 'Since 1 year',
  CUSTOM = 'Custom'
}

@Component({
  selector: 'cintas-timeline-selection',
  templateUrl: './timeline-selection.component.html',
  styleUrls: ['./timeline-selection.component.scss']
})
export class TimelineSelectionComponent implements OnInit {

  faCalendar = faCalendar;
  ChartPeriod = ChartPeriod;

  times = [ChartPeriod.NOW, ChartPeriod.THIS_WEEK, ChartPeriod.THIS_MONTH, ChartPeriod.LAST_MONTH, ChartPeriod.THIS_YEAR, ChartPeriod.LAST_YEAR, ChartPeriod.SINCE_1_WEEK, ChartPeriod.SINCE_2_WEEKS, ChartPeriod.SINCE_1_MONTH, ChartPeriod.SINCE_3_MONTHS, ChartPeriod.SINCE_1_YEAR, ChartPeriod.CUSTOM];

  @Input()
  timeSelection: ChartPeriod = ChartPeriod.NOW;

  @Output()
  timeSelectionChanged: EventEmitter<ChartPeriod> = new EventEmitter<ChartPeriod>();

  constructor() {
  }

  ngOnInit() {
  }

  onTimeSelected(t: ChartPeriod) {
    this.timeSelection = t;
    this.timeSelectionChanged.emit(this.timeSelection);
  }

}
