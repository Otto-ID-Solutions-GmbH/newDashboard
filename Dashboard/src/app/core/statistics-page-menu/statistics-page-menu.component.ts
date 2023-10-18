import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {faFileExport} from "@fortawesome/free-solid-svg-icons";
import * as _ from "lodash";
import {faCircleNotch} from "@fortawesome/free-solid-svg-icons/faCircleNotch";
import {faPrint} from "@fortawesome/free-solid-svg-icons/faPrint";
import {ChartPeriod} from "../timeline-selection/timeline-selection.component";

export type FeatureSet = {
  timeline?: boolean,
  export?: boolean,
  print?: boolean,
  customers?: boolean
};

@Component({
  selector: 'cintas-statistics-page-menu',
  templateUrl: './statistics-page-menu.component.html',
  styleUrls: ['./statistics-page-menu.component.scss']
})
export class StatisticsPageMenuComponent implements OnInit {

  faFileExport = faFileExport;
  loadingIcon = faCircleNotch;
  printIcon = faPrint;

  @Input()
  exportLoadingIndicator: boolean = false;

  @Input()
  set features(value: FeatureSet) {
    _.merge(this._features, value);
  }

  get features(): FeatureSet {
    return this._features;
  }

  private _features: FeatureSet = {
    timeline: false,
    export: false,
    print: true
  };

  @Input()
  title: string;

  @Input()
  timeSelection: ChartPeriod = ChartPeriod.NOW;

  @Output()
  timeSelectionChanged: EventEmitter<string> = new EventEmitter<string>();

  @Output()
  exportClicked: EventEmitter<any> = new EventEmitter<any>();

  @Output()
  customerSelect: EventEmitter<any> = new EventEmitter<any>();

  constructor() {
  }

  ngOnInit() {
  }

  onTimeSelected(t) {
    this.timeSelection = t;
    this.timeSelectionChanged.emit(this.timeSelection);
  }

  onExportClicked() {
    this.exportClicked.emit();
  }

  onPrintClicked() {
    window.print();
  }

  onCustomerSelected(customer) {
    this.customerSelect.emit(customer);
  }

}
