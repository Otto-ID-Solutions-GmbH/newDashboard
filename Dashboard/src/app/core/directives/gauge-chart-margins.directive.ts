import {Directive, Self} from '@angular/core';
import {GaugeComponent} from "@swimlane/ngx-charts";

@Directive({
  selector: '[cintasGaugeChartMargins]'
})
export class GaugeChartMarginsDirective {

  constructor(@Self() chart: GaugeComponent) {
    chart.margin = [0, 0, 0, 0];
  }

}
