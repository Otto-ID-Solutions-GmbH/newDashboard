import {Component} from '@angular/core';
import * as _ from "lodash";
import {KPIS} from "./core/kpi-modules";
import {faBarcode} from "@fortawesome/free-solid-svg-icons/faBarcode";
import {faTachometerAlt} from "@fortawesome/free-solid-svg-icons/faTachometerAlt";
import {faBars} from "@fortawesome/free-solid-svg-icons/faBars";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'cintas-client';

  iconSize = "lg";

  kpis = _.filter(KPIS, (i => i.active));
  kpiKeys = _.keys(this.kpis);

  dashboardIcon = faTachometerAlt;
  rfidReadIcon = faBarcode;
  menuIcon = faBars;

}
