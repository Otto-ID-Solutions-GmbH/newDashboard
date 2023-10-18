import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {StatisticsPageMenuComponent} from './statistics-page-menu/statistics-page-menu.component';
import {FontAwesomeModule} from "@fortawesome/angular-fontawesome";
import {BsDropdownModule} from "ngx-bootstrap/dropdown";
import {NgxChartsModule} from "@swimlane/ngx-charts";
import {DashCardComponent} from './dash-card/dash-card.component';
import {RouterModule} from "@angular/router";
import {PieChartMarginsDirective} from './directives/pie-chart-margins.directive';
import {GaugeChartMarginsDirective} from './directives/gauge-chart-margins.directive';
import {FillParentContainerDirective} from './directives/fill-parent-container.directive';
import {ExportsService} from "./services/exports.service";
import {FacilityService} from "./services/facility.service";
import {CustomerSelectionComponent} from './customer-selection/customer-selection.component';
import {TimelineSelectionComponent} from './timeline-selection/timeline-selection.component';
import {ChartBoxComponent} from './chart-box/chart-box.component';
import {PopoverModule} from 'ngx-bootstrap/popover';
import {ProductTypeSelectionComponent} from './product-type-selection/product-type-selection.component';
import {HttpClientModule} from '@angular/common/http';
import { ExportButtonComponent } from './export-button/export-button.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    HttpClientModule,
    FontAwesomeModule,
    BsDropdownModule.forRoot(),
    NgxChartsModule,
    PopoverModule.forRoot()
  ],
  declarations: [StatisticsPageMenuComponent, DashCardComponent, PieChartMarginsDirective, GaugeChartMarginsDirective, FillParentContainerDirective, CustomerSelectionComponent, TimelineSelectionComponent, ChartBoxComponent, ProductTypeSelectionComponent, ExportButtonComponent],
  exports: [StatisticsPageMenuComponent, DashCardComponent, PieChartMarginsDirective, GaugeChartMarginsDirective, FillParentContainerDirective, CustomerSelectionComponent, TimelineSelectionComponent, ChartBoxComponent, ProductTypeSelectionComponent, ExportButtonComponent],
  providers: [ExportsService, FacilityService]
})
export class CoreModule {
}
