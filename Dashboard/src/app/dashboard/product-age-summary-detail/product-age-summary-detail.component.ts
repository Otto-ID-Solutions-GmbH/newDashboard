import {Component, OnInit} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {ExportsService} from "../../core/services/exports.service";
import {map} from "rxjs/operators";
import {ProductKpiService} from "../services/product-kpi.service";
import {environment} from "../../../environments/environment";
import {KPIS} from '../../core/kpi-modules';

@Component({
  selector: 'cintas-product-age-summary-detail',
  templateUrl: './product-age-summary-detail.component.html',
  styleUrls: ['./product-age-summary-detail.component.scss']
})
export class ProductAgeSummaryDetailComponent implements OnInit {

  chartInfo = KPIS.productAgeSummary.description;

  features: FeatureSet = {
    export: true
  };

  exportLoading = false;

  data$;

  view = environment.theme.baseView;
  colorScheme = environment.theme.colorPalette;

  // options
  showLegend = true;

  constructor(private productService: ProductKpiService, private exportService: ExportsService) {
  }

  ngOnInit() {
    this.data$ = this.productService.getAvgCycleCountByProduct().pipe(
      map((res: any) => {
        return res.data;
      })
    );
  }

  onExport() {
    this.exportLoading = true;
    this.exportService.exportAgeSummaryReport()
      .subscribe(response => {
        this.exportLoading = false;
        this.exportService.saveToFileSystem(response);
      }, err => {
        this.exportLoading = false;
      });
  }


}
