import {Component, OnInit} from '@angular/core';
import {ItemKpiService} from "../services/item-kpi.service";
import {map} from "rxjs/internal/operators";
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {environment} from "../../../environments/environment";
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-items-at-location',
  templateUrl: './items-at-location.component.html',
  styleUrls: ['./items-at-location.component.scss']
})
export class ItemsAtLocationComponent implements OnInit {

  itemsAtLocation$;
  itemsAtCustomers$;

  noItemPerProduct$;

  features: FeatureSet = {
    //export: true
  };

  chartInfoAtLocations = KPIS.itemsAtLocation.description.atLocations;
  chartInfoAtCustomers = KPIS.itemsAtLocation.description.atCustomers;
  chartInfoAtSitesPerProduct = KPIS.itemsAtLocation.description.perProduct;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  // options
  showLegend = true;

  // pie
  showLabels = true;
  explodeSlices = false;
  doughnut = false;

  constructor(private itemService: ItemKpiService) {
  }

  ngOnInit() {
    this.itemsAtLocation$ = this.itemService.getItemsAtFacility(environment.facilityCuid).pipe(map((res: any) => res.data));
    this.itemsAtCustomers$ = this.itemService.getItemsAtLocations(null, 'LaundryCustomer', 1).pipe(map((res: any) => res.data));

    this.noItemPerProduct$ = this.itemService.getNoItemsPerCustomer().pipe(map((res: any) => res.data));
  }

  onSelect(event) {
    console.log(event);
  }

}
