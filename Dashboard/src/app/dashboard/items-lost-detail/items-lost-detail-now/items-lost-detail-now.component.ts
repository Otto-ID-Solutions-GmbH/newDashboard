import {Component, EventEmitter, Input, OnDestroy, OnInit, Output} from '@angular/core';
import {ItemKpiService} from "../../services/item-kpi.service";
import {Subscription} from "rxjs/index";
import {environment} from "../../../../environments/environment";
import {map} from "rxjs/operators";
import {KPIS} from '../../../core/kpi-modules';

@Component({
  selector: 'cintas-items-lost-detail-now',
  templateUrl: './items-lost-detail-now.component.html',
  styleUrls: ['./items-lost-detail-now.component.scss']
})
export class ItemsLostDetailNowComponent implements OnInit, OnDestroy {

  @Input()
  limit: number = environment.kpiParameters.itemLooseDays;

  @Output()
  customerSelected: EventEmitter<string> = new EventEmitter();

  chartInfoPerSite = KPIS.itemsLost.description.perSite;
  chartInfoTopN = KPIS.itemsLost.description.topLocations;
  chartInfoPerProductType = KPIS.itemsLost.description.perProductType;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  itemsLostQuerySub: Subscription;
  itemsLostQueryRes;

  topLocations$;

  noItemPerProduct$;

  constructor(private itemService: ItemKpiService) {
  }

  ngOnInit() {
    this.itemsLostQuerySub = this.itemService.getNoOfLostAndExistingItems(this.limit)
      .subscribe((res: any) => {
        this.itemsLostQueryRes = res.data;
      });

    this.noItemPerProduct$ = this.itemService.getNoOfLostItemsPerCustomer().pipe(map((res: any) => res.data));

    this.topLocations$ = this.itemService.getTopLocationsWithLostItems().pipe(map((res: any) => res.data));
  }

  onSelect(event) {
    console.info("Cust", event);
    this.customerSelected.emit(event);
  }

  ngOnDestroy(): void {
    if (this.itemsLostQuerySub) {
      this.itemsLostQuerySub.unsubscribe();
    }
  }

}
