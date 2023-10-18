import {
  Component,
  ContentChild,
  HostBinding,
  Input,
  OnChanges,
  OnInit,
  SimpleChanges,
  TemplateRef
} from '@angular/core';
import {faAngleRight} from "@fortawesome/free-solid-svg-icons/faAngleRight";

@Component({
  selector: 'cintas-dash-card',
  templateUrl: './dash-card.component.html',
  styleUrls: ['./dash-card.component.scss']
})
export class DashCardComponent implements OnInit, OnChanges {

  @HostBinding('class')
  parentClass = 'col d-flex col-12 col-md-6 col-xl my-3';

  @ContentChild(TemplateRef, {static: false}) chartRef;

  @Input()
  title: string;

  @Input()
  icon;

  @Input()
  label: string;

  @Input()
  kpiNumber: number;

  @Input()
  link: string;

  @Input()
  hideText: boolean = false;

  @Input()
  data;

  @Input()
  style: 'one-col' | 'two-col' = 'two-col';

  dataLoaded = false;

  detailIcon = faAngleRight;

  constructor() {
  }

  ngOnInit() {
    if (this.style == 'one-col') {
      this.parentClass = 'col d-flex col-12 col-xl my-3';
    }
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes.data) {
      if (this.data && this.data.length) {
        this.dataLoaded = true;
      } else {
        this.dataLoaded = false;
      }

    }
  }

}
