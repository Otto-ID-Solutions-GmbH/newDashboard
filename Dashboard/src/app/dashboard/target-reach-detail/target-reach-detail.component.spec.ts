import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TargetReachDetailComponent } from './target-reach-detail.component';

describe('TargetReachDetailComponent', () => {
  let component: TargetReachDetailComponent;
  let fixture: ComponentFixture<TargetReachDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TargetReachDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TargetReachDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
