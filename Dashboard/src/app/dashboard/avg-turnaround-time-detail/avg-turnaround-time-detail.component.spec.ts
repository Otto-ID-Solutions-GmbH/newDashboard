import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AvgTurnaroundTimeDetailComponent } from './avg-turnaround-time-detail.component';

describe('AvgTurnaroundTimeDetailComponent', () => {
  let component: AvgTurnaroundTimeDetailComponent;
  let fixture: ComponentFixture<AvgTurnaroundTimeDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AvgTurnaroundTimeDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AvgTurnaroundTimeDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
