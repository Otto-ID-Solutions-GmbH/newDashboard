import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';

@Component({
  selector: 'cintas-avg-turnaround-time-table',
  templateUrl: './avg-turnaround-time-table.component.html',
  styleUrls: ['./avg-turnaround-time-table.component.scss']
})
export class AvgTurnaroundTimeTableComponent implements OnInit, OnChanges {

  @Input()
  avgTurnaroundTime;

  totalAvgTurnarounds = 0;
  totalAvgTurnaroundTime = 0;

  constructor() {
  }

  ngOnInit() {
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes.avgTurnaroundTime && this.avgTurnaroundTime) {
      for (let i of this.avgTurnaroundTime) {
        this.totalAvgTurnarounds += i.avg_no_turnarounds;
        this.totalAvgTurnaroundTime += i.value;
      }
      this.totalAvgTurnarounds = this.totalAvgTurnarounds / this.avgTurnaroundTime.length;
      this.totalAvgTurnaroundTime = this.totalAvgTurnaroundTime / this.avgTurnaroundTime.length;
    }
  }

}
