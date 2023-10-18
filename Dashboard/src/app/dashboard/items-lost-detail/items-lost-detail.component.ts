import {Component, OnDestroy, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {faIndustry} from "@fortawesome/free-solid-svg-icons/faIndustry";
import {Subscription} from "rxjs";
import {ItemKpiService} from "../services/item-kpi.service";
import {ChartPeriod} from "../../core/timeline-selection/timeline-selection.component";

@Component({
  selector: 'cintas-items-lost-detail',
  templateUrl: './items-lost-detail.component.html',
  styleUrls: ['./items-lost-detail.component.scss']
})
export class ItemsLostDetailComponent implements OnInit, OnDestroy {

  features: FeatureSet = {
    export: false,
    timeline: true
  };

  timeSelection: ChartPeriod = ChartPeriod.NOW;

  customerIcon = faIndustry;
  currentCustomer;
  customersSub: Subscription;
  customers: any[];


  constructor(private itemService: ItemKpiService) {
  }

  ngOnInit() {
    this.customersSub = this.itemService.getLocationsWithLostItems().subscribe((res: any) => {
      this.customers = res.data;
      return res.data;
    });
  }

  onCustomerSelected(customerCuid) {
    if (customerCuid === 'Unknown') {
      this.currentCustomer = {
        name: 'Unknown',
        __typename: 'Unknown',
        label: 'Unknown',
        cuid: 'Unknown'
      }
    } else {
      this.currentCustomer = this.customers.find(c => c.cuid === customerCuid);
    }
  }

  ngOnDestroy(): void {
    if (this.customersSub) {
      this.customersSub.unsubscribe();
    }
  }

}
