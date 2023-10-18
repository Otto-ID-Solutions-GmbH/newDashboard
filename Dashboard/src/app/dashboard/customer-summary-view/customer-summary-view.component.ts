import {Component, OnDestroy, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {environment} from "../../../environments/environment";
import {ItemKpiService} from "../services/item-kpi.service";
import {map} from "rxjs/operators";
import {Subscription} from "rxjs";
import {ChartPeriod} from "../../core/timeline-selection/timeline-selection.component";
import {ProductKpiService} from "../services/product-kpi.service";
import {StatisticsService} from '../services/statistics.service';
import {ProductType} from '../../core/models/product-type.model';
import {ExportsService} from '../../core/services/exports.service';

@Component({
  selector: 'cintas-customer-summary-view',
  templateUrl: './customer-summary-view.component.html',
  styleUrls: ['./customer-summary-view.component.scss']
})
export class CustomerSummaryViewComponent implements OnInit, OnDestroy {

  selectedCustomer;

  timeSelection: ChartPeriod = ChartPeriod.SINCE_1_MONTH;
  timeSelectionAtSite: ChartPeriod = ChartPeriod.THIS_YEAR;
  timeSelectionIncomingOutgoing: ChartPeriod = ChartPeriod.THIS_YEAR;
  productTypeIncomingOutgoing: ProductType;

  summaryDate = Date();

  features: FeatureSet = {
    // export: true,
    timeline: false,
    customers: true
  };

  view = environment.theme.baseView;
  viewLimit = [200, 100];
  colorScheme = environment.theme.colorPalette;

  // options
  showLegend = false;

  // pie
  showLabels = true;
  explodeSlices = false;
  doughnut = false;

  private noItemPerProduct$;
  private noItemsPerProductSub: Subscription;
  noItemsPerProduct: any[];

  noLostItems$;

  avgTurnaroundTime$;

  itemsAtSiteOverTimeData$;
  exportItemsAtLocationLoading;

  incomingOutgoingOverTimeData$;
  exportItemsDeliveredReturnedLoading;


  constructor(private itemKpiService: ItemKpiService, private productKpiService: ProductKpiService,
              private statServices: StatisticsService, private exportService: ExportsService) {
  }

  ngOnInit() {

  }

  ngOnDestroy(): void {
    if (this.noItemsPerProductSub) {
      this.noItemsPerProductSub.unsubscribe();
    }
  }

  onCustomerSelected(customer) {
    this.selectedCustomer = customer;
    this.noItemPerProduct$ = this.itemKpiService.getNoItemsPerCustomer(customer.cuid);
    this.noItemsPerProductSub = this.noItemPerProduct$.subscribe(res => {
      this.noItemsPerProduct = res.data[0].series;
    });

    this.noLostItems$ = this.itemKpiService.getNoOfLostItemsForLocation(this.selectedCustomer.__typename, this.selectedCustomer.cuid).pipe(map((res: any) => res.data));
    this.onPeriodChanged(this.timeSelection);
    this.onPeriodNoItemsChanged(this.timeSelectionAtSite);
    this.onPeriodIncomingOutgoindOverTimeChanged(this.timeSelectionAtSite);
  }

  onPeriodChanged(period: ChartPeriod) {
    this.timeSelection = period;
    this.avgTurnaroundTime$ = this.productKpiService.getAvgTurnaroundTimePerProductType(this.timeSelection).pipe(
      map((res: any) => {
        let data: any[] = res.data;
        return data;
      })
    );
  }

  onPeriodNoItemsChanged(period: ChartPeriod) {
    this.timeSelectionAtSite = period;
    this.itemsAtSiteOverTimeData$ = this.itemKpiService.getNoItemsOverTime(this.selectedCustomer.__typename, this.selectedCustomer.cuid, this.timeSelectionAtSite).pipe(
      map((res: any) => {
        return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
      }));
  }

  onPeriodIncomingOutgoindOverTimeChanged(period: ChartPeriod) {
    this.timeSelectionIncomingOutgoing = period;
    map((res: any) => {
      // return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
      return res.data;
    });
  }

  onTypeChange(t: ProductType) {
    this.productTypeIncomingOutgoing = t;
    this.incomingOutgoingOverTimeData$ = this.itemKpiService.getIncomingOutgoingItemsOverTime(this.selectedCustomer.__typename, this.selectedCustomer.cuid, this.productTypeIncomingOutgoing.cuid, this.timeSelectionIncomingOutgoing).pipe(
      map((res: any) => {
        return res.data;
      }));
  }

  onExportItemsAtLocation() {
    this.exportItemsAtLocationLoading = true;
    this.exportService.exportItemsAtLocationData(this.selectedCustomer.__typename, this.selectedCustomer.cuid, this.timeSelectionAtSite)
      .subscribe(response => {
        this.exportItemsAtLocationLoading = false;
        this.exportService.saveToFileSystem(response);
      }, err => {
        this.exportItemsAtLocationLoading = false;
      });
  }

  onExportIncomingOutgoingItems() {
    this.exportItemsDeliveredReturnedLoading = true;
    this.exportService.exportIncomingOutgoingProductsOverTimeData(this.selectedCustomer.__typename, this.selectedCustomer.cuid, this.productTypeIncomingOutgoing.cuid, this.timeSelectionIncomingOutgoing)
      .subscribe(response => {
        this.exportItemsDeliveredReturnedLoading = false;
        this.exportService.saveToFileSystem(response);
      }, err => {
        this.exportItemsDeliveredReturnedLoading = false;
      });
  }

  changeView(view) {
    this.viewLimit = view;
  }

}
