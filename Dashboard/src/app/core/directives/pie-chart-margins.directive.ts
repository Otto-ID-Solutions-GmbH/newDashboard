import {Directive, Self} from '@angular/core';
import {PieChartComponent} from '@swimlane/ngx-charts';

@Directive({
  selector: '[cintasPieChartMargins]'
})
export class PieChartMarginsDirective {

  constructor(@Self() chart: PieChartComponent) {
    chart.margins = [0, 0, 0, 0];
  }

}
