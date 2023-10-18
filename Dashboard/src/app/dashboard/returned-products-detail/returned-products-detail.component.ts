import {Component, OnInit} from '@angular/core';
import {ChartPeriod} from '../../core/timeline-selection/timeline-selection.component';
import {KPIS} from '../../core/kpi-modules';
import {FeatureSet} from '../../core/statistics-page-menu/statistics-page-menu.component';
import {environment} from '../../../environments/environment';
import {ProductKpiService} from '../services/product-kpi.service';
import {map} from 'rxjs/operators';

@Component({
  selector: 'cintas-returned-products-detail',
  templateUrl: './returned-products-detail.component.html',
  styleUrls: ['./returned-products-detail.component.scss']
})
export class ReturnedProductsDetailComponent implements OnInit {

  timeSelection: ChartPeriod = ChartPeriod.SINCE_2_WEEKS;

  chartInfo = KPIS.returnedProducts.description;

  features: FeatureSet = {
    export: false,
    timeline: true,
    customers: false
  };

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  returnedProductsData$;

  constructor(private productStatisticsService: ProductKpiService) {
  }

  ngOnInit() {
    this.returnedProductsData$ = this.productStatisticsService.getReturnedProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

  timeselectionChange(selection: ChartPeriod) {
    this.timeSelection = selection;
    this.returnedProductsData$ = this.productStatisticsService.getReturnedProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

}
