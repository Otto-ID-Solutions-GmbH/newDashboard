import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {faSpinner} from '@fortawesome/free-solid-svg-icons/faSpinner';
import {faFileExport} from '@fortawesome/free-solid-svg-icons/faFileExport';

@Component({
  selector: 'cintas-export-button',
  templateUrl: './export-button.component.html',
  styleUrls: ['./export-button.component.scss']
})
export class ExportButtonComponent implements OnInit {

  faFileExport = faFileExport;
  loadingIcon = faSpinner;

  @Input()
  title = 'Export';

  @Input()
  description = 'Export data';

  @Input()
  exportLoadingIndicator: boolean = false;

  @Output()
  exportClicked: EventEmitter<any> = new EventEmitter<any>();

  constructor() {
  }

  ngOnInit() {
  }

  onExportClicked() {
    this.exportClicked.emit();
  }

}
