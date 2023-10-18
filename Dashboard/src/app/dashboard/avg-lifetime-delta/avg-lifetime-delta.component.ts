import {Component, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {environment} from "../../../environments/environment";
import {map} from "rxjs/operators";
import {ProductKpiService} from "../services/product-kpi.service";
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-avg-lifetime-delta',
  templateUrl: './avg-lifetime-delta.component.html',
  styleUrls: ['./avg-lifetime-delta.component.scss']
})
export class AvgLifetimeDeltaComponent implements OnInit {

  items$;

  features: FeatureSet = {
    export: true
  };

  chartInfo = KPIS.productLifetimeDelta.description;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  constructor(private productService: ProductKpiService) {
  }

  ngOnInit() {
    this.items$ = this.productService.getAvgRemaingLifetimePerProduct().pipe(
      map((res: any) => res.data)
    );
  }

  onSelect(event) {
    console.log(event);
  }

}
