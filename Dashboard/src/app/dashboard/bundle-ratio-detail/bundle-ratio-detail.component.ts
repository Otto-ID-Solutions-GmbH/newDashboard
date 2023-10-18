import {Component, OnInit} from '@angular/core';
import {FeatureSet} from '../../core/statistics-page-menu/statistics-page-menu.component';
import {KPIS} from '../../core/kpi-modules';
import {ChartPeriod} from '../../core/timeline-selection/timeline-selection.component';
import {environment} from '../../../environments/environment';
import * as d3 from 'd3-shape';
import {Observable} from 'rxjs';
import {TargetKpiService} from '../services/target-kpi.service';
import {StatisticsService} from '../services/statistics.service';
import {map} from 'rxjs/operators';

@Component({
  selector: 'cintas-bundle-ratio-detail',
  templateUrl: './bundle-ratio-detail.component.html',
  styleUrls: ['./bundle-ratio-detail.component.scss']
})
export class BundleRatioDetailComponent implements OnInit {

  features: FeatureSet = {
    //export: true,
    timeline: true
  };

  chartInfo = KPIS.bundleRatio.description;

  timeSelection: ChartPeriod = ChartPeriod.SINCE_2_WEEKS;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;
  curve = d3.curveLinear;

  data$: Observable<any>;
  startDate;
  endDate;

  constructor(private targetKpiSrvice: TargetKpiService, public statServices: StatisticsService) {
  }

  ngOnInit() {
    this.onPeriodChanged(this.timeSelection);
  }

  onSelect(event) {
    console.log(event);
  }

  onPeriodChanged(period: ChartPeriod) {
    this.timeSelection = period;
    this.data$ = this.targetKpiSrvice.getBundleRatio(this.timeSelection)
      .pipe(
        map((res: any) => {
          return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
        })
      );
  }

}
