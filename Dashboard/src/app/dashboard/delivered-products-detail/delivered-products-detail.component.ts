import {Component, OnInit} from '@angular/core';
import {ChartPeriod} from '../../core/timeline-selection/timeline-selection.component';
import {FeatureSet} from '../../core/statistics-page-menu/statistics-page-menu.component';
import {environment} from '../../../environments/environment';
import {ProductKpiService} from '../services/product-kpi.service';
import {map} from 'rxjs/operators';
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-delivered-products-detail',
  templateUrl: './delivered-products-detail.component.html',
  styleUrls: ['./delivered-products-detail.component.scss']
})
export class DeliveredProductsDetailComponent implements OnInit {

  timeSelection: ChartPeriod = ChartPeriod.SINCE_2_WEEKS;

  chartInfo = KPIS.deliveredProducts.description;

  summaryDate = Date();

  features: FeatureSet = {
    export: false,
    timeline: true,
    customers: false
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

  deliveredProductsData$;

  constructor(private productStatisticsService: ProductKpiService) {
  }

  ngOnInit() {
    this.deliveredProductsData$ = this.productStatisticsService.getDeliveredProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

  timeselectionChange(selection: ChartPeriod) {
    this.timeSelection = selection;
    this.deliveredProductsData$ = this.productStatisticsService.getDeliveredProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

}
