import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ScanActionsListComponent } from './scan-actions-list.component';

describe('ScanActionsListComponent', () => {
  let component: ScanActionsListComponent;
  let fixture: ComponentFixture<ScanActionsListComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ScanActionsListComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ScanActionsListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
