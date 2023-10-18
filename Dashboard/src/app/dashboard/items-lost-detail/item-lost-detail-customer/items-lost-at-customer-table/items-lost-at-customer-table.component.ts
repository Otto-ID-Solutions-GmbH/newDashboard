import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';
import {forEach} from "lodash-es";

@Component({
  selector: 'cintas-items-lost-at-customer-table',
  templateUrl: './items-lost-at-customer-table.component.html',
  styleUrls: ['./items-lost-at-customer-table.component.scss']
})
export class ItemsLostAtCustomerTableComponent implements OnInit, OnChanges {

  @Input()
  data = [];

  noLostItemsSum = 0;
  totalAllowance = 0;
  accountableSum = 0;
  noMissingItemsSum = 0;
  noEarlyWarningItemsSum = 0;

  constructor() {
  }

  ngOnInit() {
  }

  ngOnChanges(changes: SimpleChanges): void {
    if(changes.data && this.data) {
      this.noLostItemsSum = 0;
      this.totalAllowance = 0;
      this.accountableSum = 0;
      this.noMissingItemsSum = 0;
      this.noEarlyWarningItemsSum = 0;
      for (let d of this.data) {
        this.noLostItemsSum += d.no_lost_items_90;
        this.noMissingItemsSum += d.no_lost_items_60;
        this.noEarlyWarningItemsSum += d.no_lost_items_30;
        this.totalAllowance = this.noLostItemsSum * 0.01;
        this.accountableSum = this.noLostItemsSum - this.totalAllowance;
      }
    }
  }

}
