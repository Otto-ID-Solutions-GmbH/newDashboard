import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';
import {ItemKpiService} from "../../services/item-kpi.service";
import {map} from "rxjs/internal/operators";
import {environment} from "../../../../environments/environment";
import {ChartPeriod} from "../../../core/timeline-selection/timeline-selection.component";
import {StatisticsService} from "../../services/statistics.service";
import {KPIS} from '../../../core/kpi-modules';

@Component({
  selector: 'cintas-items-lost-detail-timeline',
  templateUrl: './items-lost-detail-timeline.component.html',
  styleUrls: ['./items-lost-detail-timeline.component.scss']
})
export class ItemsLostDetailTimelineComponent implements OnInit, OnChanges {

  @Input()
  limit: number = environment.kpiParameters.itemLooseDays;

  @Input()
  timeStep: ChartPeriod = ChartPeriod.THIS_WEEK;

  chartInfo = KPIS.itemsLost.description.timeline;

  view = environment.theme.baseView;

  colorScheme = environment.theme.colorPalette;
  itemsLostTime$;

  startDate;
  endDate;
  interval;

  constructor(private itemService: ItemKpiService, private statServices: StatisticsService) {
  }

  ngOnInit() {

  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes.timeStep || changes.limit) {
      this.itemsLostTime$ = this.itemService.getNoLostItemsPerTime(this.limit, this.timeStep)
        .pipe(
          map((res: any) => {
            this.startDate = res.meta.start_date;
            this.endDate = res.meta.end_date;
            this.interval = res.meta.interval;
            return JSON.parse(JSON.stringify(res), this.statServices.reviver).data;
          }));
    }
  }

  onSelect(event) {
    console.log(event);
  }

}
