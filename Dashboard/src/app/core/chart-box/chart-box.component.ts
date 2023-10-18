import {Component, Input, OnInit} from '@angular/core';
import {faInfoCircle} from '@fortawesome/free-solid-svg-icons/faInfoCircle';

@Component({
  selector: 'cintas-chart-box',
  templateUrl: './chart-box.component.html',
  styleUrls: ['./chart-box.component.scss']
})
export class ChartBoxComponent implements OnInit {

  @Input()
  chartInfoTitle: string;

  @Input()
  chartInfo: string;

  infoIcon = faInfoCircle;

  infoIsOpen = false;

  constructor() {
  }

  ngOnInit() {
  }

  onShow() {
    this.infoIsOpen = true;
  }

  onHide() {
    this.infoIsOpen = false;
  }

}
