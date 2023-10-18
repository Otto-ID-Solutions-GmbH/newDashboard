import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';
import {environment} from "../../../../environments/environment";
import {ItemKpiService} from "../../services/item-kpi.service";
import {map} from "rxjs/operators";
import {KPIS} from '../../../core/kpi-modules';

@Component({
  selector: 'cintas-item-lost-detail-customer',
  templateUrl: './item-lost-detail-customer.component.html',
  styleUrls: ['./item-lost-detail-customer.component.scss']
})
export class ItemLostDetailCustomerComponent implements OnInit, OnChanges {

  @Input()
  customer: string | any;

  @Input()
  limit: number = environment.kpiParameters.itemLooseDays;

  chartInfo = KPIS.itemsLost.description.atSite;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  data$;

  constructor(private itemService: ItemKpiService) {
  }

  ngOnInit() {

  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes.customer) {
      this.data$ = this.itemService.getNoOfLostItemsForLocation(this.customer.__typename, this.customer.cuid).pipe(map((res: any) => res.data))
    }
  }

  onSelect(event) {
    console.log(event);
  }

}
