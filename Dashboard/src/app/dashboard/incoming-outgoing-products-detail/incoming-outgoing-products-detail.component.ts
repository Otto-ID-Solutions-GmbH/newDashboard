import {Component, OnInit} from '@angular/core';
import {ChartPeriod} from '../../core/timeline-selection/timeline-selection.component';
import {KPIS} from '../../core/kpi-modules';
import {FeatureSet} from '../../core/statistics-page-menu/statistics-page-menu.component';
import {environment} from '../../../environments/environment';
import {ProductKpiService} from '../services/product-kpi.service';
import {map} from 'rxjs/operators';

@Component({
  selector: 'cintas-incoming-outgoing-products-detail',
  templateUrl: './incoming-outgoing-products-detail.component.html',
  styleUrls: ['./incoming-outgoing-products-detail.component.scss']
})
export class IncomingOutgoingProductsDetailComponent implements OnInit {

  timeSelection: ChartPeriod = ChartPeriod.SINCE_2_WEEKS;

  chartInfo = KPIS.incomingOutgoingProducts.description;

  features: FeatureSet = {
    export: false,
    timeline: true,
    customers: false
  };

  view = environment.theme.baseView;
  incomingOutgoingGoodsColorScheme = environment.theme.incomingOutgoingGoods;

  incomingOutgoingProductsData$;

  constructor(private productStatisticsService: ProductKpiService) {
  }

  ngOnInit() {
    this.incomingOutgoingProductsData$ = this.productStatisticsService.getIncomingOutgoingProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

  timeselectionChange(selection: ChartPeriod) {
    this.timeSelection = selection;
    this.incomingOutgoingProductsData$ = this.productStatisticsService.getIncomingOutgoingProductsInPeriod(this.timeSelection).pipe(
      map((res: any) => res.data)
    );
  }

}
