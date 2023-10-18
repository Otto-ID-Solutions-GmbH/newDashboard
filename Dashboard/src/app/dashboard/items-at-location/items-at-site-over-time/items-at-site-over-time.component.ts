import {Component, OnInit} from '@angular/core';
import {environment} from '../../../../environments/environment';
import {ChartPeriod} from '../../../core/timeline-selection/timeline-selection.component';
import {KPIS} from '../../../core/kpi-modules';
import {map} from 'rxjs/operators';
import {FeatureSet} from '../../../core/statistics-page-menu/statistics-page-menu.component';
import {ItemKpiService} from '../../services/item-kpi.service';
import {StatisticsService} from '../../services/statistics.service';

@Component({
  selector: 'cintas-items-at-site-over-time',
  templateUrl: './items-at-site-over-time.component.html',
  styleUrls: ['./items-at-site-over-time.component.scss']
})
export class ItemsAtSiteOverTimeComponent implements OnInit {

  timeSelection: ChartPeriod = ChartPeriod.THIS_YEAR;

  chartInfo = KPIS.incomingOutgoingProducts.description;

  features: FeatureSet = {
    export: false,
    timeline: true,
    customers: false
  };

  view = environment.theme.baseView;
  colorScheme = environment.theme.incomingOutgoingGoods;

  itemsAtSiteOverTimeData$;

  startDate;
  endDate;
  interval;

  constructor(private itemService: ItemKpiService, private statServices: StatisticsService) {
  }

  ngOnInit() {
    this.itemsAtSiteOverTimeData$ = this.itemService.getNoItemsOverTime('LaundryCustomer', 'cjpl2rcjx000e90qyy8yf51m2', this.timeSelection).pipe(
      map((res: any) => {
        this.startDate = res.meta.start_date;
        this.endDate = res.meta.end_date;
        this.interval = res.meta.interval;
        return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
      }));
  }

  timeselectionChange(selection: ChartPeriod) {
    this.timeSelection = selection;
    this.itemsAtSiteOverTimeData$ = this.itemService.getNoItemsOverTime('LaundryCustomer', 'cjpl2rcjx000e90qyy8yf51m2', this.timeSelection).pipe(
      map((res: any) => {
        this.startDate = res.meta.start_date;
        this.endDate = res.meta.end_date;
        this.interval = res.meta.interval;
        return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
      }));
  }

}
