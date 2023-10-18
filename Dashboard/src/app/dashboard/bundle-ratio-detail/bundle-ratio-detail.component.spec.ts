import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BundleRatioDetailComponent } from './bundle-ratio-detail.component';

describe('BundleRatioDetailComponent', () => {
  let component: BundleRatioDetailComponent;
  let fixture: ComponentFixture<BundleRatioDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BundleRatioDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BundleRatioDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
