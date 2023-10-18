import {Component, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {environment} from "../../../environments/environment";
import {ProductKpiService} from "../services/product-kpi.service";
import {map} from "rxjs/operators";
import {ChartPeriod} from "../../core/timeline-selection/timeline-selection.component";
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-avg-turnaround-time-detail',
  templateUrl: './avg-turnaround-time-detail.component.html',
  styleUrls: ['./avg-turnaround-time-detail.component.scss']
})
export class AvgTurnaroundTimeDetailComponent implements OnInit {

  features: FeatureSet = {
    //export: true,
    timeline: true
  };

  chartInfo = KPIS.avgTurnaroundTime.description;

  timeSelection: ChartPeriod = ChartPeriod.SINCE_1_MONTH;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  avgTurnaroundTime$;

  constructor(private productService: ProductKpiService) {
  }

  ngOnInit() {
    this.onPeriodChanged(this.timeSelection);
  }

  onSelect(event) {
    console.log(event);
  }

  onPeriodChanged(period: ChartPeriod) {
    this.timeSelection = period;
    this.avgTurnaroundTime$ = this.productService.getAvgTurnaroundTimePerProductType(this.timeSelection).pipe(
      map((res: any) => {
        let data: any[] = res.data;
        return data;
      })
    );
  }

}
