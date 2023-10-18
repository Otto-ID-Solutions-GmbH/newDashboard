import {
  AfterViewInit,
  ChangeDetectorRef,
  Directive,
  ElementRef,
  EventEmitter,
  HostListener,
  Inject,
  Input,
  Output
} from '@angular/core';
import {DOCUMENT} from "@angular/common";

@Directive({
  selector: '[cintasFillParentContainer]'
})
export class FillParentContainerDirective implements AfterViewInit {

  @Input()
  widthRatio;

  @Output()
  parentSizeChanged: EventEmitter<any> = new EventEmitter();

  currentView = [];

  @HostListener('window:resize')
  onResize() {
    this.onElementChanged(this.el.nativeElement);
  }

  constructor(private el: ElementRef, private cd: ChangeDetectorRef, @Inject(DOCUMENT) private doc: Document) {

  }

  ngAfterViewInit(): void {
    this.onElementChanged(this.el.nativeElement);
  }

  onElementChanged(nativeElement) {
    let width = this.getParentWidth(nativeElement);
    let height;

    if (this.widthRatio) {
      height = width / this.widthRatio;
    } else {
      height = this.getParentHeight(nativeElement);
    }


    if (this.currentView[0] != width || this.currentView[1] != height) {
      this.currentView = [
        width,
        height
      ];
      this.parentSizeChanged.emit(this.currentView);
      this.cd.detectChanges();
    }
  }

  private getParentWidth(element) {
    let pL = parseFloat(window.getComputedStyle(element).getPropertyValue('padding-left'));
    let pR = parseFloat(window.getComputedStyle(element).getPropertyValue('padding-right'));
    return element.clientWidth - pL - pR;
  }

  private getParentHeight(element) {
    let pT = parseFloat(window.getComputedStyle(element).getPropertyValue('padding-top'));
    let pB = parseFloat(window.getComputedStyle(element).getPropertyValue('padding-bottom'));
    return element.clientHeight - pT - pB;
  }

}
