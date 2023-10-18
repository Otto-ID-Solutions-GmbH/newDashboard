import {Component, OnDestroy, OnInit} from '@angular/core';
import {ItemKpiService} from "../services/item-kpi.service";
import {map} from "rxjs/internal/operators";
import * as _ from "lodash";
import {Observable, Subscription} from "rxjs/index";
import {environment} from "../../../environments/environment";
import {ProductKpiService} from "../services/product-kpi.service";
import {TargetKpiService} from "../services/target-kpi.service";
import {faClock} from "@fortawesome/free-solid-svg-icons/faClock";
import {KPIS} from "../../core/kpi-modules";
import {StatisticsService} from '../services/statistics.service';
import * as d3 from 'd3-shape';

@Component({
  selector: 'cintas-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit, OnDestroy {

  // options
  showLegend = false;
  colorScheme = environment.theme.colorPalette;
  incomingOutgoingGoodsColorScheme = environment.theme.incomingOutgoingGoods;

  dashboardView = [100, 100];

  viewLimit = [200, 100];

  // Items At location
  itemsAtLocation$;
  itemsAtLocationTotalCount;
  itemsAtCustomers$;
  itemsAtCustomersTotalCount;
  showLabels = false;
  explodeSlices = false;
  doughnut = false;

  // Items lost
  itemsLostCustomersQuerySub: Subscription;
  itemsLostCustomerQueryRes = [];
  noTotalItemsCustomer: number;
  noLostItemsCustomer: number;

  itemsLostLocationQuerySub: Subscription;
  itemsLostLocationQueryRes = [];
  noTotalItemsLocation: number;
  noLostItemsLocation: number;

  // Avg. Lifetime
  lifeTime$;

  // Targetcontainer Reach
  targetContainerReachToday$;
  totalTargetReach = null;

  // Avg. Turnaround Time
  avgTurnaroundTime$;
  totalAvgTurnaroundTime = 0;

  groupedCycleCount$;
  totalAvgCycleCount = null;

  // Delivered and returned Products
  deliveredProductsData$;
  returnedProductsData$;
  incomingOutgoingProductsData$;
  barPadding = 0;
  barGroupPadding = 2;

  // Bundle ratio
  bundleRatioData$: Observable<any>;
  curve = d3.curveLinear;

  // Icons
  lifeTimeDelta = faClock;

  kpis = KPIS;

  constructor(private itemService: ItemKpiService, private productService: ProductKpiService, private targetKpiService: TargetKpiService, public statServices: StatisticsService) {
  }

  ngOnInit() {

    this.itemsAtLocation$ = this.itemService.getItemsAtFacility(environment.facilityCuid).pipe(map((res: any) => {
      let locations = res.data;
      this.itemsAtLocationTotalCount = _.reduce(res.data, (acc, cur) => {
        return acc + cur.value;
      }, 0);
      return locations;
    }));

    this.itemsAtCustomers$ = this.itemService.getItemsAtLocations(null, 'LaundryCustomer', 1).pipe(map((res: any) => {
      let locations = res.data;
      this.itemsAtCustomersTotalCount = _.reduce(res.data, (acc, cur) => {
        return acc + cur.value;
      }, 0);
      return locations;
    }));

    this.itemsLostCustomersQuerySub = this.itemService.getNoOfLostItems(environment.kpiParameters.itemLooseDays, 'LaundryCustomer', 1)
      .subscribe((res: any) => {
        this.itemsLostCustomerQueryRes = res.data.chart_data;
        this.noLostItemsCustomer = res.data.no_lost_items;
        this.noTotalItemsCustomer = res.data.no_items;
      });

    this.itemsLostLocationQuerySub = this.itemService.getNoOfLostItems(environment.kpiParameters.itemLooseDays, 'Facility', 0)
      .subscribe((res: any) => {
        this.itemsLostLocationQueryRes = res.data.chart_data;
        this.noLostItemsLocation = res.data.no_lost_items;
        this.noTotalItemsLocation = res.data.no_items;
      });

    this.lifeTime$ = this.productService.getAvgRemaingLifetimePerProduct().pipe(
      map((res: any) => res.data)
    );

    this.targetContainerReachToday$ = this.targetKpiService.getContainerTargetReachToday().pipe(
      map((res: any) => {
        this.totalTargetReach = res.data.total_avg_reach;
        return res.data.chart_data;
      })
    );

    this.bundleRatioData$ = this.targetKpiService.getAggregatedBundleRatio().pipe(
      map((res: any) => {
        return res.data;
      })
    );

    this.avgTurnaroundTime$ = this.productService.getAvgTurnaroundTimePerProductType().pipe(
      map((res: any) => {
        let data: any[] = res.data;
        this.totalAvgTurnaroundTime = !data ? 0 : data.reduce((result, product) => {
          return result + product.value;
        }, 0) / data.length;
        return data;
      })
    );

    this.groupedCycleCount$ = this.productService.getGroupedCycleCount().pipe(
      map((res: any) => {
        this.totalAvgCycleCount = res.data.total_avg_cycle_count;
        return res.data.chart_data
      })
    );

    this.deliveredProductsData$ = this.productService.getDeliveredProductsInPeriod().pipe(
      map((res: any) => res.data)
    );

    this.returnedProductsData$ = this.productService.getReturnedProductsInPeriod().pipe(
      map((res: any) => res.data)
    );

    this.incomingOutgoingProductsData$ = this.productService.getIncomingOutgoingProductsInPeriod().pipe(
      map((res: any) => res.data)
    );

  }


  onSelect(event) {
    console.log(event);
  }

  ngOnDestroy(): void {
    if (this.itemsLostCustomersQuerySub) {
      this.itemsLostCustomersQuerySub.unsubscribe();
    }
  }

  totalReach(val) {
    return this.totalTargetReach;
  }

  changeView(view) {
    this.viewLimit = view;
  }

}
