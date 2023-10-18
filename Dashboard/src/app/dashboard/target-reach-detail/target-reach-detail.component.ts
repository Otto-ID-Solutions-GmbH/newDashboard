import {Component, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {environment} from "../../../environments/environment";
import {Observable} from "rxjs";
import {TargetKpiService} from "../services/target-kpi.service";
import {map} from "rxjs/operators";
import * as d3 from 'd3-shape';
import {StatisticsService} from "../services/statistics.service";
import {ChartPeriod} from "../../core/timeline-selection/timeline-selection.component";
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-target-reach-detail',
  templateUrl: './target-reach-detail.component.html',
  styleUrls: ['./target-reach-detail.component.scss']
})
export class TargetReachDetailComponent implements OnInit {

  features: FeatureSet = {
    //export: true,
    timeline: true
  };

  chartInfo = KPIS.containerTargetReach.description;

  timeSelection: ChartPeriod = ChartPeriod.THIS_WEEK;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;
  curve = d3.curveStep;

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
    this.data$ = this.targetKpiSrvice.getTargetReachPerScan(this.timeSelection)
      .pipe(
        map((res: any) => {
          this.startDate = res.meta.start_date;
          this.endDate = res.meta.end_date;
          return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
        })
      );
  }

}
